# frozen_string_literal: true

require_relative './user_rest_client'

class FetchUserTweets
  include UserRestClient

  def initialize(user_rest_client = create_user_rest_client)
    @user_rest_client = user_rest_client
  end

  def call(collection = [], batch_size: 200, min_id: nil, max_id: nil, &block)
    tweets = user_rest_client.user_timeline(timeline_api_params(min_id: min_id, max_id: max_id, batch_size: batch_size))
    return collection if tweets.empty?

    yield(tweets)     if block_given?
    call(collection + tweets, batch_size: batch_size, min_id: min_id, max_id: tweets.last.id - 1, &block)
  end

  private

  attr_reader :user_rest_client

  def timeline_api_params(min_id:, max_id:, batch_size:)
    since_id = min_id.nil? ? nil : min_id - 1
    {
      since_id: since_id,
      max_id: max_id,
      include_rts: true,
      count: batch_size,
      tweet_mode: :extended
    }.compact
  end
end
