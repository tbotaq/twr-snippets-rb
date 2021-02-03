describe FetchUserFolloweeIds do
  describe '#call' do
    let(:instance) { described_class.new(twitter_rest_client) }
    subject { instance.call([], initial_cursor) }

    let(:twitter_rest_client) { instance_double(Twitter::REST::Client) }
    let(:initial_cursor) { -1 }
    let(:terminal_cursor) { 0 }
    before do
      # emulate the first API request
      allow(twitter_rest_client).to receive(:friend_ids).with(cursor: initial_cursor).and_return(first_response)
      # emulate the second API request
      allow(twitter_rest_client).to receive(:friend_ids).with(cursor: first_response.attrs.fetch(:next_cursor)).and_return(second_response)

      # spy #call method to count the number of times it is called
      allow(instance).to receive(:call).and_call_original
    end
    let(:first_response) { instance_double(Twitter::Cursor, attrs: { ids: [1, 2], next_cursor: 123_456 }) }
    let(:second_response) { instance_double(Twitter::Cursor, attrs: { ids: [3, 4], next_cursor: terminal_cursor }) }

    it 'calls #call method recursively' do
      subject
      expect(instance).to have_received(:call).with([], initial_cursor).once
      expect(instance).to have_received(:call).with(first_response.attrs.fetch(:ids),
                                                    first_response.attrs.fetch(:next_cursor)).once
      expect(instance).to have_received(:call).twice
    end
    it 'returns all the collected ids' do
      is_expected.to contain_exactly(*(first_response.attrs.fetch(:ids) + second_response.attrs.fetch(:ids)))
    end
  end
end
