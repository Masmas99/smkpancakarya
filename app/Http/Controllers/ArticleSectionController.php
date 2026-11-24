<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

abstract class ArticleSectionController extends Controller
{
    /**
     * Slug bagian publik sekaligus kategori artikel.
     */
    abstract protected function section(): string;

    /**
     * Model Eloquent yang menyimpan konten bagian ini.
     *
     * @return class-string<Article>
     */
    abstract protected function modelClass(): string;

    abstract protected function title(): string;

    abstract protected function description(): string;

    abstract protected function emptyMessage(): string;

    public function index(): View
    {
        $model = $this->modelClass();

        $articles = $model::published()
            ->with('author')
            ->latest('published_at')
            ->simplePaginate(9);

        return view('pages.landing.'.$this->section().'.index', [
            'articles' => $articles,
            'title' => $this->title(),
            'description' => $this->description(),
            'empty' => $this->emptyMessage(),
        ]);
    }

    public function show(string $article): View|RedirectResponse
    {
        $model = $this->modelClass();

        $found = $model::where('slug', $article)->first();

        if (! $found) {
            foreach (Article::models() as $candidate) {
                if ($candidate === $model) {
                    continue;
                }

                $other = $candidate::published()->where('slug', $article)->first();

                if ($other) {
                    return redirect()->to($other->url(), 301);
                }
            }

            abort(404);
        }

        abort_unless($found->isPublished(), 404);

        if ($found->section() !== $this->section()) {
            return redirect()->to($found->url(), 301);
        }

        return view('pages.landing.'.$this->section().'.show', ['article' => $found]);
    }
}
