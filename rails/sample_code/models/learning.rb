class Learning < ActiveRecord::Base
  include PgSearch
  acts_as_followable
  acts_as_paranoid
  attr_accessor :delete_image

  belongs_to :learning_category
  has_many :account_learnings

  has_many :learning_tags
  has_many :learning_tag_skills,  -> { where(learning_tag_type: "Skill") }, class_name: "LearningTag"
  has_many :learning_tag_goals,  -> { where(learning_tag_type: "Goal") }, class_name: "LearningTag"
  has_many :learning_tag_classifications,  -> { where(learning_tag_type: "Classification") }, class_name: "LearningTag"

  has_many :skills, :through => :learning_tag_skills
  has_many :goals, :through => :learning_tag_goals
  has_many :classifications, :through => :learning_tag_classifications

  accepts_nested_attributes_for :learning_tag_skills, allow_destroy: true
  accepts_nested_attributes_for :learning_tag_goals, allow_destroy: true
  accepts_nested_attributes_for :learning_tag_classifications, allow_destroy: true


  validates :serial_no, presence: true, uniqueness: true
  validates :title, :learning_category_id,  presence: true
  validates :difficult_level, numericality: { greater_than: 0 }
  validates :url_link, format: { with: /\A#{URI::regexp(['http', 'https'])}\z/, message: "is wrong!" }

  has_attached_file :image, :default_url => ''
  validates_attachment :image,
                       :content_type => { content_type: /\Aimage\/.*\Z/ },
                       :size => { in: 0..10240.kilobytes }
  before_validation { image.clear if delete_image.to_i == 1}

  scope :by_key, ->(key) {where("LOWER(title) like :key", key: "%#{key.downcase}%")}
  scope :by_category, -> (category_id) {where(learning_category_id: category_id)}
  [:original].each do |style|
    define_method("#{style}_photo_url") do
      Rails.env.development? ? image.url(style) : image.expiring_url(10*60, style)
    end
  end

  def is_read?(account)
    account_learnings.where(account_id: account.id).present?
  end

  pg_search_scope :search_full_text, against: [:title, :description], :using => { :tsearch => { :prefix => true } }


  class << self
    def not_read_by(account)
      learnings = Learning.joins(:account_learnings).where(account_learnings: { account_id: account.id })
      Learning.where.not(id: learnings.select(:id))
    end

    def joins_account account, **opts
      filters = (opts[:filters] || []) & ['latest', 'marked']
      select_query = <<-SQL.squish
        learnings.*, (follows.id IS NOT NULL) AS is_marked
      SQL

      join_query = <<-SQL.squish
        LEFT JOIN follows ON follows.followable_id = learnings.id AND follows.followable_type = 'Learning' AND follows.follower_type = 'Account' AND follows.follower_id = #{account.id}
      SQL

      where_queries = if filters.empty?
        []
      elsif filters.include?("marked")
        ['follows.id is not NULl']
      end

      if filters && filters.include?("latest")
        order_latest = "learnings.created_at desc"
      else
        order_latest = ""
      end
      select(select_query).joins(join_query).where(where_queries).order(order_latest)
    end
  end

  def is_marked_by? account
    read_attribute('is_marked').nil? ? followed_by?(account) : read_attribute('is_marked')
  end
end
