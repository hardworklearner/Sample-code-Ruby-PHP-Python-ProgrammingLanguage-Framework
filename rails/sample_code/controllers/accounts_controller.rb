class AccountsController < BaseController
  before_action :set_post, only: [:like]
  before_action :set_account, only: [:my_profile, :edit, :update]

  def my_profile
    @published_total = current_account.projects.count
    @favorite_tocal = Like.where(liker_type: "Account", liker_id: current_account.id).count
    @comment_total = current_account.comments.count
    @certifications = current_account.account_certifications.includes(:certification)
  end

  def show
    @account = Account.find params[:id]
    @published_total = @account.projects.count
    @favorite_tocal = Like.where(liker_type: "Account", liker_id: @account.id).count
    @comment_total = @account.comments.count
    @certifications = @account.account_certifications.includes(:certification)
  end

  def edit
    @account_skill = AccountSkill.new
  end

  def update
    if @account.update(account_params)
      flash[:success] = "Profile Saved!"
      redirect_to my_profile_accounts_path
    else
      flash[:error] = "Update fail"
      render :edit
    end
  end

  def my_posts
    @posts = Project.joins_account(current_account, self: true)
                    .includes(:account, last_comment: [:account])
                    .order(created_at: :desc).page(params[:page]).per(5)
  end

  def my_bookmarks
    @posts = Project.joins_account(current_account).where(follows: {follower_id: current_account.id })
                    .includes(:account, last_comment: [:account])
                    .order(created_at: :desc).page(params[:page]).per(5)
  end

  def my_likes
    @posts = Project.joins_account(current_account).where(likes: {liker_id: current_account.id })
                    .includes(:account, last_comment: [:account])
                    .order(created_at: :desc).page(params[:page]).per(5)
  end

  def list_collaborators
    if params[:getid]
      @post = Project.find(params[:getid])
      @list_collaborators = @post.project_collaborators.where(state: "accepted").includes(:account)
      @list_requesteds = @post.project_collaborators.where(state: "requested").includes(:account)
      respond_to do |format|
        format.js
        format.html
      end
    end
  end

  private
  def set_post
    @post = Project.find params[:id]
  end

  def set_account
    @account = Account.find current_account.id
  end

  def account_params
    params.require(:account).permit( :id, :name, :email, :mobile_no, :gender, :maker_level_id, :roles_mask, :school_id, :avatar,
                                      account_skills_attributes: [:id, :level, :skill_id, :_destroy] )
  end
end
