<h1>Bookmarks tagged with <?= $this->Text->toList(h($tags)) ?></h1>

<section>
<?php foreach ($bookmarks as $bookmark): ?>
    <article>
        <h4><?= $this->Html->link($bookmark->title, $bookmark->url) ?></h4>
        <small><?= h($bookmark->url) ?></small>
        <p><?= $this->Text->autoParagraph(h($bookmark->description)) ?></p>
    </article>
<?php endforeach; ?>
</section>
