# Letter Avatar for PHP

Generate user avatar using name initials letter.

![letter-avatar](https://cloud.githubusercontent.com/assets/618412/12192012/835c7488-b60d-11e5-9276-d06f42d11a86.png)

## Features
* Data URI image ready (also save as PNG/JPG).
* Consistent color.
* Customize size, shape: square, circle.
* Small, fast.

## Install

Via Composer

``` bash
$ composer require NIOLAB/letter-avatar
```

### Implementation

``` php
<?php

use NIOLAB\LetterAvatar\LetterAvatar;

$avatar = new LetterAvatar('Steven Spielberg');

// Square Shape, Size 64px
$avatar = new LetterAvatar('Steven Spielberg', 'square', 64);

// Save Image As PNG/JPEG
$avatar->saveAs('path/to/filename');
$avatar->saveAs('path/to/filename', "image/jpeg");

```

``` html
<img src="<?php echo $avatar ?>" />
```
