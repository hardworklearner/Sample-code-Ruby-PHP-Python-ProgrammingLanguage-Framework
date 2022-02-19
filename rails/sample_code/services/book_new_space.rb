class BookNewSpace
  attr_reader :account, :space, :purpose, :start_time, :end_time, :duration, :message, :booking_space, :remaining_hours

  def initialize account, space, purpose, start_time, duration
    @account = account
    @space = space
    @purpose = purpose
    @start_time = start_time
    @duration = duration.to_i
    @end_time = @start_time.advance(hours: @duration)
    @remaining_hours = @account.hours_for_booking(@start_time)
  end

  def book
    reservation_not_in_past!

    remaining_hours_greater_than_duration!

    time_is_available!

    reservation_is_available!

    create_reservation!

    true
  rescue Exception => e
    @message = e.message
    false
  end

  private
  def reservation_not_in_past!
    raise "can't book space at a moment in the past" if @end_time <= Time.zone.now
  end

  def remaining_hours_greater_than_duration!
    raise "Your remaining time is #{remaining_hours}. Cann't book this space" if remaining_hours <= 0 || remaining_hours < duration
  end

  def time_is_available!
    sql = <<-SQL.squish
      (start_time <= :start_time AND :start_time < end_time) OR
      (start_time < :end_time AND :end_time <= end_time)
    SQL
    raise "You cann't book multiple spaces at a time" if account.booking_spaces.bookings.where(sql, {start_time: start_time, end_time: end_time}).exists?
  end

  def reservation_is_available!
    spaces = Filter::AvailableSpace.new(start_time: start_time.iso8601, duration: duration, remaining_hours: remaining_hours).results
    raise "This space is not available" unless spaces.where(id: space.id).first
  end

  def create_reservation!
    raise "This space is not available for booking from #{start_time.strftime("%d-%m-%Y %H:%M")} to #{end_time.strftime("%d-%m-%Y %H:%M")}" unless space.available_for_booking?(start_time, end_time)

    @booking_space = BookingSpace.new(account: account, purpose: purpose, space: space, start_time: start_time, end_time: end_time)

    if (Time.zone.now - start_time)/1.minutes > 15
      # Auto check-in if current time > start time + 15'
      raise "Can't book this space" unless @booking_space.book && @booking_space.check_in!
    else
      raise "Can't book this space" unless @booking_space.book!
    end
  end
end
