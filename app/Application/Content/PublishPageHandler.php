<?php

namespace App\Application\Content;

use App\Application\Content\Commands\PublishPageCommand;
use App\Application\Content\Results\PublishPageResult;
use App\Domain\Content\Page;

/**
 * Handles the page publishing use-case.
 */
final class PublishPageHandler
{
    /**
     * Publish a page when all business rules are satisfied.
     *
     * @param PublishPageCommand $command Use-case input with target page ID.
     * @return PublishPageResult Use-case result with success state or failure reason.
     */
    public function handle(PublishPageCommand $command): PublishPageResult
    {
        $page = Page::find($command->pageId);

        if (! $page) {
            return PublishPageResult::pageNotFound();
        }

        if ($page->isPublished()) {
            return PublishPageResult::alreadyPublished();
        }

        if (empty($page->title)) {
            return PublishPageResult::validationFailed('Page title is required');
        }

        if (empty($page->slug)) {
            return PublishPageResult::validationFailed('Page slug is required');
        }

        $now = now();

        $page->update([
            'status' => Page::STATUS_PUBLISHED,
            'published_at' => $now,
        ]);

        return PublishPageResult::success($now);
    }
}
