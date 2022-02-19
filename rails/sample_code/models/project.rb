class Project < ActiveRecord::Base
  include PgSearch
  acts_as_paranoid
  has_paper_trail
  include AASM
  attr_accessor :check_state, :delete_image

  acts_as_likeable
  acts_as_followable
  acts_as_commentable

  belongs_to :account
  has_many   :project_collaborators, dependent: :destroy
  has_many   :project_steps, -> { order(number: :asc) }, dependent: :destroy
  has_one    :last_comment, -> { order(created_at: :desc) }, as: :commentable, class_name: 'Comment'

  validates :title, presence: true
  validates :epick, presence: true , allow_blank: true
  validates :epick_color, presence: true, allow_blank: true
  validates :epick_color, format: { with: /\A#(?:[0-9a-fA-F]{3}){1,2}\Z/ }, allow_blank: true
  validates :description, length: { maximum: 150 }
  validates :number_of_collabs, :numericality => { :greater_than_or_equal_to => 0, :less_than_or_equal_to => 9 }
  validate  :check_number_collabs
  validate  :account_with_experiences, :account_with_achievements, if: :title_changed?


  has_attached_file :image, :styles => { large: "600x600>" }, :default_url => ''
  validates_attachment :image,
                       :content_type => { content_type: /\Aimage\/.*\Z/ },
                       :size => { in: 0..10240.kilobytes }

  accepts_nested_attributes_for :project_steps, allow_destroy: true

  pg_search_scope :search_full_text, against: [:title, :description], :using => { :tsearch => { :prefix => true } }
  scope :ideas, -> { joins_psteps.where('COALESCE(psteps.steps_count, 0) = 0') }
  scope :makes, -> { joins_psteps.where('psteps.steps_count > 0') }
  scope :epicks, -> { where('projects.epick IS NOT NULL') }
  scope :collabs, -> { where("number_of_collabs > 0 AND collabs_count < number_of_collabs AND projects.state = 'created'") }

  before_validation { image.clear if delete_image.to_i == 1 }

  delegate :name, to: :account, prefix: true, allow_nil: true

  aasm column: :state, whiny_transitions: false do
    state :created, initial: true
    state :completed
    state :abandoned

    event :complete do
      transitions from: :created, to: :completed
    end

    event :abandon do
      transitions from: :created, to: :abandoned
    end
  end

  class << self
    def joins_psteps
      join_query = <<-SQL.squish
        LEFT JOIN (
          SELECT ps.project_id, COUNT(*) AS steps_count
          FROM project_steps ps
          GROUP BY ps.project_id
        ) psteps ON psteps.project_id = projects.id
      SQL
      joins(join_query)
    end

    def joins_account account, **opts
      filters = (opts[:filters] || []) & ['liked', 'commented']

      select_query = <<-SQL.squish
        projects.*, (likes.id IS NOT NULL) AS is_liked, (follows.id IS NOT NULL) AS is_marked, COALESCE(pcollabs.state, '') AS collab_state
      SQL

      join_comments = <<-SQL.squish
        INNER JOIN (
          SELECT DISTINCT comments.commentable_id AS project_id
          FROM comments
          WHERE commentable_type = 'Project' AND account_id = #{account.id}
        ) pcm ON pcm.project_id = projects.id
      SQL

      join_query = <<-SQL.squish
        LEFT JOIN likes ON likes.likeable_id = projects.id AND likes.likeable_type = 'Project' AND likes.liker_type = 'Account' AND likes.liker_id = #{account.id}
        LEFT JOIN follows ON follows.followable_id = projects.id AND follows.followable_type = 'Project' AND follows.follower_type = 'Account' AND follows.follower_id = #{account.id}
        LEFT JOIN project_collaborators pcollabs ON pcollabs.project_id = projects.id AND pcollabs.account_id = #{account.id}
      SQL

      join_query += ' ' + join_comments if filters.include?('commented')

      where_queries = if opts[:self]
        ["(projects.account_id = #{account.id} OR pcollabs.state = 'accepted')"]
      else
        ["(projects.is_private = false)"]
      end
      where_queries << 'likes.id IS NOT NULL' if filters.include?('liked')
      where_queries << "follows.id is not NULl" if opts[:selected_type] && opts[:selected_type] == "marked"

      order_query = opts[:sort] == 'like' ? 'projects.likers_count DESC' : 'projects.created_at DESC'

      select(select_query).joins(join_query).where(where_queries.join(' AND ')).order(order_query)
    end
  end

  [:large, :original].each do |style|
    define_method("#{style}_photo_url") do
      Rails.env.development? ? image.url(style) : image.expiring_url(10*60, style)
    end
  end

  def is_liked_by? account
    read_attribute('is_liked').nil? ? liked_by?(account) : read_attribute('is_liked')
  end

  def is_marked_by? account
    read_attribute('is_marked').nil? ? followed_by?(account) : read_attribute('is_marked')
  end

  def collab_state account
    read_attribute('collab_state') || project_collaborators.where(account: account).first.try(:state) || ''
  end

  def duplicate
    duplicator = ProjectDuplicator.new(self)
    duplicator.duplicate
  end

  private
  def account_with_experiences
    if title == "Experiences" && Project.exists?(account_id: account_id, title: title)
      errors.add(:title, "experiences is duplicated")
    end
  end

  def account_with_achievements
    if title == "Achievements" && Project.exists?(account_id: account_id, title: title)
      errors.add(:title, "achievements is duplicated")
    end
  end

  def check_number_collabs
    if self.number_of_collabs.to_i < self.project_collaborators.where(state: 'accepted').count
      errors.add(:number_of_collabs, 'Please remove existing member to reduce number of total collabs !')
    else
      true
    end
  end
end
