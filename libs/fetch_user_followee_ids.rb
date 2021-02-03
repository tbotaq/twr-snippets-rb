# frozen_string_literal: true

require_relative './user_rest_client'

class FetchUserFolloweeIds
  include UserRestClient

  FRIEND_API_INITIAL_CURSOR  = -1
  FRIEND_API_TERMINAL_CURSOR = 0
  # These cursors are defined in the doc of twitter api. see: https://developer.twitter.com/en/docs/pagination
  private_constant :FRIEND_API_INITIAL_CURSOR, :FRIEND_API_TERMINAL_CURSOR

  def initialize(user_rest_client = create_user_rest_client)
    @user_rest_client = user_rest_client
  end

  def call(ids = [], cursor = FRIEND_API_INITIAL_CURSOR)
    followees   = user_rest_client.friend_ids(cursor: cursor)
    ids        += followees.attrs[:ids]
    next_cursor = followees.attrs[:next_cursor]
    return ids if next_cursor == FRIEND_API_TERMINAL_CURSOR

    call(ids, next_cursor)
  end

  private

  attr_reader :user_rest_client
end
