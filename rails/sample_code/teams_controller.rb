class Pages::TeamsController < Pages::BaseController
  before_action :set_team, only: [:show, :edit, :update, :destroy, :search_info, :update_team_leader]
  before_action :set_quarter, only: [:index, :show]
  before_action :permission_leader_admin, only: [:update_team_leader]
  before_action :session_okr_of_all_team, only: [:index]
  before_action :check_tour_guide

  include PermissionsHelper

  def index
    if !params[:year].present? && session[:period_objective_of_all_team].present? && 
    	!session[:period_objective_of_all_team]["[#{Date.today.year}, #{(Date.today.month / 3.0).ceil}]"].present?
      period_params = session[:period_objective_of_all_team].first[0].to_s.gsub("[","").gsub("]","").split(", ")
      if period_params[0].to_i.present? && period_params[1].to_i.present?
        redirect_to pages_teams_path(year: period_params[0].to_i, quarter: period_params[1].to_i)
      end
    end
    @teams   = TeamServices::GetTeams.new(current_account.company_id).get_all(@current_quarter[:period_quarter], @current_quarter[:period_year])
    @quarter = params[:quarter] || QuarterTime::QUARTER
    @year    = params[:year]    || QuarterTime::YEAR
  end

  def show
    return redirect_to pages_my_team_path(@team, year: @current_quarter[:period_year], 
    	quarter: @current_quarter[:period_quarter]) if @team.members.pluck(:account_id).include?(current_account.id)
    total_list = 0
    @objs = ObjectiveProviders::GetObjectivesOfTeam.new(@team.id, @current_quarter[:period_quarter], 
    	@current_quarter[:period_year]).get_objectives_detect_level()
    @obj_main_count = 0
    @obj_sub_count = 0
    @total_points = 0
    @objs.each do |ob|
      if ob.main?
        @obj_main_count += 1 
      end
      @obj_sub_count += 1 if ob.sub?
    end
    @objectives = ObjectiveProviders::GetObjectivesOfTeam.new(@team.id, @current_quarter[:period_quarter], @current_quarter[:period_year]).run(@objs)
    @objectives.each do |oo|
      unless oo.back_burner?
        total_list += 1
        @total_points += oo.current_point
      end
    end

    @current_point = total_list > 0 ? (@total_points/total_list).round(1) : 0.0
    
    start_quarter = Date.parse("#{QuarterTime::YEAR}-#{QuarterTime::QUARTER * 3 - 2}-1")

    special_progress_status = @objectives.pluck(:progress_status).uniq == ['back_burner']
    @white_timeline = (Date.today - start_quarter).ceil || 150
    @progress_status = special_progress_status ? :back_burner : CalculationServices::ProgressStatus.(@current_quarter[:period_quarter], @current_quarter[:period_year], @current_point) if @current_point
    @permission = permission_assigned_team(current_account, @team)

    @count_members = @team.accounts.where("members.role = ?", 0).pluck(:id).uniq.count
  end

  def destroy
    if AccessProviders::PermissionOfTeams.new(current_account).can_delete?(@team)
      @team.destroy
    else
      flash[:error] = "You are not authorized to do this. Please contact your Team Leader or Administrator for help."
    end
    redirect_to [:pages, :teams]
  end

  def search_info
    @type = params[:type].downcase
    if @type == 'team_leader'
      @record = Account.where(status: [:pending, :activate])
                       .where('first_name like ? or last_name like ? or fullname like ? or email like ?', "%#{params[:search].downcase}%", "%#{params[:search].downcase}%", "%#{params[:search].downcase}%", "%#{params[:search].downcase}%")
                       .where(company: current_account.company)
                       .order(fullname: :asc, email: :asc)
                       .distinct
    elsif @type == "department"
      @record = Department.activate.where("name like ? ", "%#{params[:search].downcase}%").where(company: current_account.company).order(name: :asc).distinct
    elsif @type == "member"
      if params[:search].present?
        @record = @team.company
                      .accounts
                      .where(status: [:pending, :activate])
                      .where('first_name like ? or last_name like ? or fullname like ? or email like ?', "%#{params[:search].downcase}%", "%#{params[:search].downcase}%", "%#{params[:search].downcase}%", "%#{params[:search].downcase}%")
                      .order(fullname: :asc, email: :asc)
                      .distinct
      else
        @record = @team.accounts
                       .where(status: [:pending, :activate])
                       .where('accounts.first_name like ? or accounts.last_name like ? or accounts.fullname like ? or accounts.email like ?', "%#{params[:search].downcase}%", "%#{params[:search].downcase}%", "%#{params[:search].downcase}%", "%#{params[:search].downcase}%")
                       .order(fullname: :asc, email: :asc)
                       .distinct
      end
    end
  end

  def update_department
    @team = Team.find_by_id(params[:id])
    return unless AccessProviders::PermissionOfTeams.new(current_account).can_update_department_of_team?(@team)
    @team.update department_id: params[:department_id]
  end

  def update_team_name
    @team     = Team.find(params[:id])
    if AccessProviders::PermissionOfTeams.new(current_account).can_update_name?(@team)
      init_name = @team.name
      if @team.update name: params[:name]
        render json: { success: true, team_name: @team.name }
      else      
        render json: { error: @team.errors.full_messages.join('<br>'), team_name: init_name }
      end
    else
      render json: { error: "You are not authorized to do this. Please contact your Team Leader or Administrator for help." }
    end
  end

  def toggle_link_member
    @team = Team.find(params[:id])
    return unless AccessProviders::PermissionOfTeams.new(current_account).can_invite_to_team?(@team)
    user_id = params[:user_id].to_i
    return unless user_id
    if params[:type] == "unlink"
      owner_ids = @team.objectives.pluck(:owner_id).uniq
      if owner_ids.include?(user_id)
        return render json: { status: false }
      else
        new_account_ids = @team.account_ids.reject{|x| x == user_id }
      end
    else
      new_account_ids = @team.account_ids.push(user_id)
    end
    @team.update account_ids: new_account_ids
  end

  def remove_invite_member
    team = Team.find_by_id params[:id]
    render json: {
      status: team.present? ? team.objectives.pluck(:owner_id).uniq.include?(params[:member_id].to_i) ? false : true : true
    }
  end

  def remove_invite_leader
    team = Team.find_by_id params[:id]
    render json: {
      status: team.present? ? team.objectives.pluck(:owner_id).uniq.include?(params[:member_id].to_i) ? false : true : true
    }
  end

  def check_exist_leader_team
    render json: { leader: Member.find_by(team_id: params[:team_id], role: 1).nil?, id: params[:team_id] }
  end

  def load_team_for_invite_user
    @team = Team.where(company_id: current_account.company_id).activate.where('name like ?',"%#{params[:search]}%")
  end

  private

  def set_team
    @team = Team.activate.includes(:company).where(company: current_account.company).distinct.find_by_id params[:id]
    redirect_to pages_teams_path unless @team
  end

  def set_quarter
    if params[:year].present? && params[:quarter].present?
      @current_quarter = { period_year: params[:year], period_quarter: params[:quarter] }
    else
      @current_quarter = { period_year: Time.zone.now.year, period_quarter: (Time.zone.now.month/3.to_f).ceil }
    end
    @period_quarters = Team.build_period_quarters
  end

  def permission_leader_admin
    @permission = current_account.is_admin? || current_account.leader?
  end
end
