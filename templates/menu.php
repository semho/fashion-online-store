<ul class="main-menu <?=$style?>">
    <?php foreach ($sortArray as $item) : ?>
        <li>
            <a class="main-menu__item <?php if ($item['path'] == $_SERVER["REQUEST_URI"]) {?>active<?php }?>" href=<?=$item['path']?>>
                <?=cutString($item['title'], 15); ?>
            </a>
        </li>
    <?php endforeach; ?>
</ul>
