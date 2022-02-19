class Admin::AccountsController < Admin::BaseController

  before_action :find_account, only: %i(edit update destroy)

  def index
    respond_to do |format|
      format.html
      format.json{
        render json: Admin::AccountDatatable.new(params, view_context: view_context)
      }
    end
  end

  def new
    @account = Account.new
  end
  
  def create
    company = Company.find_by_id params[:company]
    @account = Account.new account_params
    if company
      @account.company = company
      @account.company_owner = true
    end
    if @account.save
      flash[:success] = 'Saved successfully!'
      redirect_to admin_accounts_path
    else
      render :new
    end
  end

  def edit; end

  def update
    company = Company.find_by_id params[:company]
    @account.company = company if company
    if @account.update account_params
      flash[:success] = "Updated successfully!"
      redirect_to admin_accounts_path
    else
      render :edit
    end
  end

  def destroy
    if @account.destroy
      flash[:success] = "Deleted successfully!"
    else
      flash.now[:error] = @account.errors.full_messages.join(', ')
    end
    redirect_to admin_accounts_path
  end

  def team_tab
    @teams = Team.order(created_at: :desc)
  end

  def login_tracker
    @trackers = LoginActivity.where(user_type: 'Account', success: true)
                             .order(created_at: :desc)
                             
  end

  private

    def find_account
      @account = Account.find_by_id params[:id]
      return admin_accounts_path unless @account
    end

    def account_params
      params.require(:account).permit(:first_name, :last_name, :email, :company_id,
                                      :role, :status, :is_admin, :invite_by, :password, :password_confirmation
                                     )
    end

end
