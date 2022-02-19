module API
  module V1
    class Accounts < Grape::API
      include API::V1::Defaults

      resource :accounts do
        before do
          authorize_api!
        end

        desc "Get list of my likes" do
          failure [[401, 'Unauthorized', API::Entities::Error]]
          success API::Entities::ProjectsList
          headers 'Http-Auth-Token' => { description: 'API Token', required: true }
        end
        params do
          use :pagination
        end
        get "/my_likes" do
          projects = Project.joins_account(current_account).where(likes: {liker_id: current_account.id })
      										.includes(account: [:maker_level, :account_skills], project_steps: [:project_step_images])
      										.order(created_at: :desc).page(params[:page]).per(params[:per_page])
          present Hash(page: projects.current_page || 1, per_page: params[:per_page] || 25, data: projects), with: API::Entities::ProjectsList, show_account: true, account: current_account
        end

        desc "Get list of my bookmarks" do
          failure [[401, 'Unauthorized', API::Entities::Error]]
          success API::Entities::MyBookmarksList
          headers 'Http-Auth-Token' => { description: 'API Token', required: true }
          detail "type: 'post', 'event', 'learning'"
        end
        params do
          use :pagination
        end
        get "/my_bookmarks" do
          query = Query::AccountBookmark.new(current_account, page: params[:page], per_page: params[:per_page])
          present Hash(page: query.page, per_page: query.per_page, data: query.call), with: API::Entities::MyBookmarksList
        end

        desc "Get hours remaining usage" do
          failure [[401, 'Unauthorized', API::Entities::Error]]
          success API::Entities::HoursRemaining
          headers 'Http-Auth-Token' => { description: 'API Token', required: true }
        end
        get "/my_hours_remaining" do
          present current_account, with: API::Entities::HoursRemaining
        end

        desc "Problem report" do
          failure [[408, 'Unauthorized', API::Entities::Error]]
          success API::Entities::Success
          headers 'Http-Auth-Token' => { description: 'API Token', required: true }
        end
        params do
          requires :message, type: String, desc: "Message"
        end
        post '/feedback' do
          report = ProblemReport.new(account: current_account, message: params[:message])
          unless report.save
            error!({ message: 'Failed', messages: [], code: 'feedback_failed', with: API::Entities::Error }, 422)
          end
          present Hash(message: "We have received your message and will reply as soon as possible.!", messages: [], code: 'sent_email'), with: API::Entities::Success
        end
      end
    end
  end
end
