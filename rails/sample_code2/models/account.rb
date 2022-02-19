class Account < ApplicationRecord
  require 'roo'
  attr_reader :teams_count, :total_account_current_month, :total_account_previous_month, :comp_name
  attr_accessor :company_name_attr, :agreed_term_of_use, :skip_password_validation, :signup_validation
  has_paper_trail

  # Include default devise modules. Others available are:
  # :confirmable, :lockable, :timeoutable, :trackable and :omniauthable
  devise :database_authenticatable, :registerable,
         :recoverable, :rememberable, :validatable, :confirmable, 
         :trackable, :omniauthable, omniauth_providers: [:google_oauth2]

  before_create :set_default_timezone
  before_save :reload_timezone, :reload_fullname

  after_destroy do
    Objective.activate.where(owner: self).delete_all
  end

  mount_uploader :avatar, AttachmentUploader, class_name: 'attachment'
  validates_integrity_of :avatar, message: "Avatar should be less than 2MB!"

  enum status: { pending: 0, activate: 1, deactivate: 2 }
  enum role: { member: 0, leader: 1 }
  enum confirmation_step: { created: 0, verified: 1, completed: 2, changing_email: 3 }

  has_many :members, dependent: :destroy
  has_many :teams, through: :members, dependent: :destroy
  has_many :leader_teams, foreign_key: "team_leader_id", class_name: "Team", dependent: :destroy
  has_many :login_activities, as: :user, dependent: :destroy
  has_many :message_boards, foreign_key: 'owner_id', class_name: 'MessageBoard', dependent: :destroy
  has_many :notifications

  belongs_to :company
  belongs_to :inviter, foreign_key: 'invite_by', class_name: 'Account', optional: true

  accepts_nested_attributes_for :company

  validates :email, format: {with: /\A[a-z0-9\+\-_\.]+@[a-z\d\-.]+\.[a-z]+\z/i, message: "invalid"}
  validates :first_name, :last_name, presence: true, on: :update
  validates :first_name, :last_name, length: { maximum: 15 }
  validates :password, presence: false
  
  validates :company_name_attr, length: {maximum: 20, too_long: "%{count} characters is the maximum allowed"}

  validate  :invalid_signup_params

  before_validation :set_company, on: [ :create ]
  before_validation :skip_password, on: [ :create ]
  before_validation :strip_whitespace, only: [:first_name, :last_name]

  delegate :name, to: :company, prefix: true, allow_nil: true
  delegate :email, :fullname, to: :inviter, prefix: true, allow_nil: true

  def self.from_google(uid:, email:, full_name:, last_name:, first_name:, avatar_url:)
    account = Account.find_or_initialize_by(email: email)
    if account.id.present?
      account.google_uid = uid unless account.google_uid.present?
    else
      if email.split("@")[1] == "gmail.com"
        company_name = "Change Company Name"
      else
        company_name = email.split("@")[1].split(".")[0].titleize
      end
      account.company_name_attr = company_name
      account.google_uid = uid
      account.last_name = last_name
      account.first_name = first_name
      account.confirmation_step = :verified
      account.skip_confirmation_notification!
      account.signup_validation = true
      account.agreed_term_of_use = "1"
      account.is_admin = true
      account.company_owner = true
    end
    unless account.save
      p account.errors.full_messages
    end
    return account
  end

  def account_valid? email
    regex = /\A[\w+\-.]+@[a-z\d\-]+(\.[a-z\d\-]+)*\.[a-z]+\z/i
    (email =~ regex).present? &&  (email =~ regex) == 0 && self.class.find_by_email(email).nil?
  end

  def set_company
    if signup_validation
      if self.company_name_attr.present? && account_valid?(email)
        company = Company.create(name: self.company_name_attr)
        self.company = company
      end
    end
  end

  def fullname
    combine_name = [self.first_name, self.last_name].join(" ")
    if combine_name == "Unknown Unknown" || !combine_name.present?
      return self.email
    else
      return combine_name
    end
  end

  def reload_fullname
    if self.first_name_changed? || self.last_name_changed?
      self.fullname = [self.first_name, self.last_name].join(" ")
    end
  end 


  def self.check_regex_email email
    email.match /\A[a-z0-9\+\-_\.]+@[a-z\d\-.]+\.[a-z]+\z/i
  end

  def self.search(options = {})
    res = self.all
    res = res.where("LCASE(email) like ?", "%#{options[:email].downcase}%") if !options[:email].blank?
    res = res.where("LOWER(CONCAT_WS('', first_name, last_name)) like ?", "%#{options[:name].downcase}%") if !options[:name].blank?
    res.page(options[:page])
  end

  def validation_email
    unless self.id.present?
      Account.where(email: self.email, confirmation_step: [])
    end
  end

  def invalid_signup_params
    if signup_validation
      unless company_name_attr.present?
        errors.add(:company_name_attr, "can't be blank")
      end

      unless agreed_term_of_use == "1"
        errors.add(:agreed_term_of_use, "Terms of Use is require to checked")
      end

      unless self.last_name.present?
        errors.add(:last_name, "can't be blank")
      end

      unless self.first_name.present?
        errors.add(:first_name, "can't be blank")
      end
    end
  end

  def self.invite_user!(current_account, user)
    return unless Account.find_by(email: user['email']).nil?
    account = Account.find_or_initialize_by(email: user["email"])
    account.assign_attributes(
      invite_by: current_account.id,
      company_id: current_account.company_id, 
      role: user["role"].downcase,
      first_name: user["first_name"], 
      last_name: user["last_name"], 
      company_owner: false, 
      is_admin: false, 
      invitation_message: user["invitation_message"] || "" )
    account.save!
    role =  user['role'] == 'Leader' && Member.find_by(team_id: user['team'],role: 1).nil? ? 1 : 0
    if user['team'].present? && user['team'].to_i > 0
      team = Member.new(account_id: account.id, team_id: user['team'], role: role)
      team.save!
    end
    account
  end

  def self.invite_new_user!(current_account, obj, type)
    account = Account.find_or_initialize_by(email: obj[:email])
    if account.new_record?
      account.assign_attributes(
        invite_by: current_account.id,
        company_id: current_account.company_id, 
        role: :member,
        first_name: nil, 
        last_name: nil, 
        company_owner: false, 
        is_admin: false, 
        invitation_message: obj[:invitation_message],
        is_notify: obj[:is_notify] )
      account.save!
      if obj[:team_id].present? && type == "invite_by_user"
        member = Member.find_or_initialize_by(account_id: account.id, team_id: obj[:team_id])
        member.role = :member
        member.save!
      end
    end
    account
  end

  def self.invite_new_leader!(current_account, obj, type)
    account = Account.find_or_initialize_by(email: obj[:email])
    if account.new_record?
      account.assign_attributes(
        invite_by: current_account.id,
        company_id: current_account.company_id, 
        role: :member,
        first_name: nil, 
        last_name: nil, 
        company_owner: false, 
        is_admin: false, 
        invitation_message: obj[:invitation_message],
        is_notify: obj[:is_notify] )
      account.save!
    end
    account
  end

  def count_team
    Team.activate.where(company_id: self.company_id).count
  end

  def count_team_user_joined
    self.members.joins(:team).count
  end

  def count_department
    Department.activate.where(company_id: self.company_id).count
  end

  def count_key_result
    Objective.where(owner: self.id, object_type: 1).count
  end

  def set_default_timezone
    self.country = "Singapore"
    self.zone_identifier = "Asia/Singapore"
    self.timezone = NotificationServices::GetTimeZone.new(self.zone_identifier).run()
  end

  def send_mail_registered_to_admin
    DeviseMailer.registered_to_admin(self).deliver_now
  end

  def reload_timezone
    if self.zone_identifier_changed?
      self.timezone = NotificationServices::GetTimeZone.new(self.zone_identifier).run()
    end
  end

  def teams_count
    read_attribute("teams_count").present? ? read_attribute("teams_count") : 0
  end

  def total_account_current_month
    read_attribute("total_account_current_month").present? ? read_attribute("total_account_current_month") : 0
  end

  def total_account_previous_month
    read_attribute("total_account_previous_month").present? ? read_attribute("total_account_previous_month") : 0
  end

  def comp_name
    read_attribute("comp_name").present? ? read_attribute("comp_name") : ""
  end

  def original_url
    return self.avatar.url if self.avatar.present?
  end

  def thumb_url
    return self.avatar.thumb.url if self.avatar.present?
  end

  def self.check_import file, current_account
    spreadsheet = Roo::Spreadsheet.open file
    header = spreadsheet.row 1
    errors =  []
    ActiveRecord::Base.transaction do
      (2..spreadsheet.last_row).each do |i|
        row = [header, spreadsheet.row(i)].transpose.to_h
        full_messages = []
        full_messages << "Email can't be blank" if row['email'].nil?
        full_messages << "Email is not valid" if row['email'].present? && check_regex_email(row['email']).nil?

        full_messages << "First name can't be blank" if row['first_name'].nil?
        full_messages << "First name is too long" if row['first_name'].present? && row['first_name'].length > 35
        # full_messages << "First name is not valid" if row['first_name'].present? && check_regex_string(row['first_name']).nil?

        full_messages << "Last name can't be blank" if row['last_name'].nil?
        full_messages << "Last name is too long" if row['last_name'].present? && row['last_name'].length > 35
        # full_messages << "Last name is not valid" if row['last_name'].present? && check_regex_string(row['last_name']).nil?

        errors << {row: i, msg: full_messages.join(', ')} if full_messages.present?
      end
    end
    return errors
  end

  def self.import_file file, current_account
    spreadsheet = Roo::Spreadsheet.open file, extension: :csv
    header = spreadsheet.row 1
    errors = []
    ActiveRecord::Base.transaction do
      (2..spreadsheet.last_row).each do |i|
        row = [header, spreadsheet.row(i)].transpose.to_h
        row['password'] = "S" + SecureRandom.alphanumeric(8)
        temp = Account.new()
        temp.email = row.first[1]
        temp.password = row['password']
        temp.company_id = current_account.company_id
        temp.first_name = row['first_name']
        temp.last_name = row['last_name']
        temp.status = 1
        temp.confirmation_step = 2
        temp.confirmed_at = Time.now
        temp.skip_confirmation!
        if temp.save
          DeviseMailer.email_imported_user(temp, row['password']).deliver_now
        end
        errors << {row: i, msg: temp.errors.full_messages.join(', '), email: temp.email} if temp.errors.full_messages.present?
      end
    end
    return errors
  end

  private

  def password_required?
    return false if skip_password_validation
    super
  end

  def strip_whitespace
    self.first_name = self.first_name.strip unless self.first_name.nil?
    self.last_name = self.last_name.strip unless self.last_name.nil?
  end

end
