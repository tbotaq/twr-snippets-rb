describe FetchUserTweets do
  describe '#call' do
    subject { instance.call(collection, min_id: min_id, max_id: max_id, batch_size: batch_size) }
    let(:instance) { described_class.new(twitter_rest_client) }
    let(:min_id) { 10 }
    let(:max_id) { 100 }
    let(:batch_size) { 100 }

    let(:twitter_rest_client) { instance_double(Twitter::REST::Client) }
    context 'API returns no tweet' do
      before { allow(twitter_rest_client).to receive(:user_timeline).and_return([]) }
      let(:collection) { 1.upto(3).map { |n| instance_double(Twitter::Tweet, n.to_s, id: n) } }
      it 'should have made an API request with correct parameter' do
        subject
        expect(twitter_rest_client).to have_received(:user_timeline).with(
          hash_including(
            since_id: min_id - 1,
            max_id: max_id,
            count: batch_size
          )
        ).once
      end
      it "doesn't yield the given block" do
        expect do |b|
          instance.call(collection, min_id: min_id, max_id: max_id, batch_size: batch_size, &b)
        end.not_to yield_control
      end
      it "doesn't make any further API request" do
        subject
        expect(twitter_rest_client).to have_received(:user_timeline).once
      end
      it 'returns the given collection' do
        is_expected.to contain_exactly(*collection)
      end
    end

    context 'API returns some tweets' do
      let(:collection) { 6.downto(4).map { |n| instance_double(Twitter::Tweet, n.to_s, id: n) } }
      let(:tweets) { 3.downto(1).map { |n| instance_double(Twitter::Tweet, n.to_s, id: n) } }
      before do
        # stub the first API request
        allow(twitter_rest_client).to receive(:user_timeline).with(
          hash_including(
            since_id: min_id - 1,
            max_id: max_id
          )
        ).and_return(tweets)

        # stub the second API request to return a blank array
        allow(twitter_rest_client).to receive(:user_timeline).with(
          hash_including(
            since_id: min_id - 1,
            max_id: tweets.last.id - 1
          )
        ).and_return([])

        # spy the #call method
        allow(instance).to receive(:call).and_call_original
      end
      it 'should have made an API request with correct parameter' do
        subject
        expect(twitter_rest_client).to have_received(:user_timeline).with(
          hash_including(
            since_id: min_id - 1,
            max_id: max_id,
            count: batch_size
          )
        ).once
      end
      it 'yields the given block' do
        expect do |b|
          instance.call(collection, min_id: min_id, max_id: max_id, batch_size: batch_size, &b)
        end.to yield_successive_args(tweets)
      end
      it 'makes the next call with correct parameter' do
        subject
        expect(instance).to have_received(:call).with(
          collection + tweets,
          min_id: min_id,
          max_id: tweets.last.id - 1,
          batch_size: batch_size
        ).once
      end
      it 'finally returns all the collected tweets' do
        is_expected.to contain_exactly(*collection, *tweets)
      end
    end
  end
end
