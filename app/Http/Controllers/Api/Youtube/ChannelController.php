<?php

namespace App\Http\Controllers\Api\Youtube;

use App\Helper\YoutubeHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Youtube\Channel\GetChannelDetailsFromID;
use App\Http\Requests\Api\Youtube\Channel\GetChannelListFromName;
use App\Http\Requests\Api\Youtube\Channel\GetMineChannelAnalytics;
use App\Http\Requests\Api\Youtube\Channel\GetMineChannelList;
use App\Http\Requests\Api\Youtube\Channel\GetTopChannelsList;

class ChannelController extends Controller
{
    public function getChannelListFromName(GetChannelListFromName $request)
    {
        $yt = new YoutubeHelper();
        $service = $yt->getYoutubeService();
        $result = $service->search->listSearch(
            'id',
            [
                'q' => $request->channelName,
                'type' => 'channel',
                'maxResults' => $request->maxResults ?? 2,
            ]
        );

        $ids = [];
        foreach ($result as $value) {
            if (empty($value->id->channelId)) {
                continue;
            }
            $ids[] = $value->id->channelId;
        }
        $ids = implode(',', $ids);

        $channelList = $service->channels->listChannels('id,snippet,statistics', ['id' => $ids]);

        return response()->json($channelList, 200);
    }

    public function getChannelDetailsFromID(GetChannelDetailsFromID $request)
    {
        $yt = new YoutubeHelper();
        $service = $yt->getYoutubeService();
        $result = $service->channels->listChannels('id,snippet,statistics,topicDetails,contentDetails', ['id' => $request->id]);
        return response()->json($result, 200);
    }

    public function getTopChannelsList(GetTopChannelsList $request)
    {
        $yt = new YoutubeHelper();
        $service = $yt->getYoutubeService();
        // $result = $service->search->listSearch(
        //     'id',
        //     [
        //         'type' => 'channel',
        //         'maxResults' => $request->maxResults ?? 10,
        //         'order' => $request->order ?? 'viewCount',
        //         'safeSearch' => 'strict'
        //     ]
        // );

        // $ids = [];
        // foreach ($result as $value) {
        //     if (empty($value->id->channelId)) {
        //         continue;
        //     }
        //     $ids[] = $value->id->channelId;
        // }
        // $ids = implode(',', $ids);

        // $channelList = $service->channels->listChannels('id,snippet,statistics', ['id' => $ids]);

        $result = $service->search->listSearch(
            'id,snippet',
            [
                'type' => 'channel',
                'maxResults' => $request->maxResults ?? 10,
                'order' => $request->order ?? 'viewCount',
                'regionCode' => 'IN',
            ]
        );

        dd($result);

        //return response()->json($channelList, 200);
    }

    public function getMineChannelList(GetMineChannelList $request)
    {
        $yt = new YoutubeHelper();
        $service = $yt->getYoutubeService(true);
        $channelList = $service->channels->listChannels('snippet,contentDetails,statistics', ['mine' => true]);
        return response()->json($channelList, 200);
    }

    public function getMineChannelAnalytics(GetMineChannelAnalytics $request)
    {
        $yt = new YoutubeHelper();
        $service = $yt->getYoutubeAnalyticsService();
        $analytics = $service->reports->query([
            'ids' => 'channel==MINE',
            'startDate' => $request->startDate,
            'endDate' => $request->endDate,
            'dimensions' => $request->dimensions ?? 'day',
            'metrics' => 'estimatedMinutesWatched,views,comments,averageViewDuration,likes,subscribersGained',
            'sort' => $request->sort ?? 'day'
        ]);
        return response()->json($analytics, 200);
    }
}
