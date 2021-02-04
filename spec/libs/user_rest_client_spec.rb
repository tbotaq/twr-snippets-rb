describe UserRestClient do
  describe '.create_user_rest_client' do
    subject { Class.new.extend(described_class).create_user_rest_client(*tokens) }

    context 'no tokens are given' do
      let(:tokens) { nil }
      it { is_expected.to be_a Twitter::REST::Client }
      it 'is totally configured with the values in ENV' do
        is_expected.to have_attributes(
          {
            consumer_key: ENV.fetch('TWITTER_CONSUMER_KEY'),
            consumer_secret: ENV.fetch('TWITTER_CONSUMER_SECRET_KEY'),
            access_token: ENV.fetch('TWITTER_ACCESS_TOKEN'),
            access_token_secret: ENV.fetch('TWITTER_ACCESS_TOKEN_SECRET')
          }
        )
      end
    end

    context 'tokens are given' do
      let(:tokens) { [access_token, access_token_secret] }
      let(:access_token) { 'access_token' }
      let(:access_token_secret) { 'access_token_secret' }
      it { is_expected.to be_a Twitter::REST::Client }
      it 'is configured with both the values in ENV and given tokens' do
        is_expected.to have_attributes(
          {
            consumer_key: ENV.fetch('TWITTER_CONSUMER_KEY'),
            consumer_secret: ENV.fetch('TWITTER_CONSUMER_SECRET_KEY'),
            access_token: access_token,
            access_token_secret: access_token_secret
          }
        )
      end
    end
  end
end
