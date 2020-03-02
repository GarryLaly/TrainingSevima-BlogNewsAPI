<?php

namespace App\Http\Controllers\Api;

use App\News;
use App\Http\Controllers\Controller;
use App\Http\Requests\NewsRequest;
use App\Http\Resources\NewsResource;

class NewsController extends Controller
{
    public function index()
    {
        $news = News::all();

        return NewsResource::collection($news);
    }

    public function indexByUser()
    {
        $news = News::where('user_id', auth()->user()->id)->get();

        return NewsResource::collection($news);
    }

    public function forbidden()
    {
        return response([
            'error' => true,
            'message' => 'Tidak memiliki akses.',
        ]);
    }

    public function show(News $news)
    {
        return new NewsResource($news);
    }

    public function store(NewsRequest $request)
    {
        $req = $request->all();

        $req['user_id'] = auth()->user()->id;
        if (isset($req['photo'])) {
            $req['photo'] = $request->file('photo')->store('news');
        }

        $news = News::create($req);

        return (new NewsResource($news))->additional([
            'message' => 'Berhasil publikasi news'
        ]);
    }

    public function update($id, NewsRequest $request)
    {
        $req = $request->all();

        $news = News::find($id);

        if (!$news) {
            return response([
                'error' => true,
                'message' => 'Berita tidak ditemukan.',
            ]);
        }else {
            if ($news->user_id != auth()->user()->id) {
                return response([
                    'error' => true,
                    'message' => 'Tidak memiliki akses ubah berita ini.',
                ]);
            }
        }

        if (isset($req['photo'])) {
            $req['photo'] = $request->file('photo')->store('news');
        }

        News::where('id', $id)->update($req);

        $news = News::find($id);

        return (new NewsResource($news))->additional([
            'message' => 'Berhasil ubah news'
        ]);
    }

    public function destroy($id)
    {
        $news = News::find($id);

        if (!$news) {
            return response([
                'error' => true,
                'message' => 'Berita tidak ditemukan.',
            ]);
        }else {
            if ($news->user_id != auth()->user()->id) {
                return response([
                    'error' => true,
                    'message' => 'Tidak memiliki akses hapus berita ini.',
                ]);
            }
        }

        $news->delete();

        return (new NewsResource($news))->additional([
            'message' => 'Berhasil hapus news'
        ]);
    }
}
