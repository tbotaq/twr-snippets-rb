# frozen_string_literal: true

require 'twitter'

module UserRestClient
  def create_user_rest_client(access_token = ENV.fetch('TWITTER_ACCESS_TOKEN'), access_token_secret = ENV.fetch('TWITTER_ACCESS_TOKEN_SECRET'))
    Twitter::REST::Client.new do |config|
      config.consumer_key        = ENV.fetch('TWITTER_CONSUMER_KEY')
      config.consumer_secret     = ENV.fetch('TWITTER_CONSUMER_SECRET_KEY')
      config.access_token        = access_token
      config.access_token_secret = access_token_secret
    end
  end
end
