<?php

namespace promocat\LetterAvatar;

use Colors\RandomColor;
use Intervention\Image\ImageManager;

class LetterAvatar {

    /**
     * @var string
     */
    protected $font;

    /**
     * @var string
     */
    protected $name;


    /**
     * @var string
     */
    protected $name_initials;


    /**
     * @var string
     */
    protected $shape;


    /**
     * @var int
     */
    protected $size;

    /**
     * @var ImageManager
     */
    protected $image_manager;

    /**
     * @var string;
     */
    protected $color;

    /**
     * @var array
     */
    protected $colorOptions  = [];

    public function __construct($name, $shape = 'circle', $size = 48, $colorOptions = [], $font = null) {
        $this->setName($name);
        $this->setImageManager(new ImageManager());
        $this->setShape($shape);
        $this->setColorOptions($colorOptions);
        $this->setSize($size);
        if (empty($font) || !file_exists($font)) {
            $font = __DIR__ . '/fonts/arial-bold.ttf';
        }
        $this->setFont($font);
    }


    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * @return ImageManager
     */
    public function getImageManager() {
        return $this->image_manager;
    }

    /**
     * @param ImageManager $image_manager
     */
    public function setImageManager(ImageManager $image_manager) {
        $this->image_manager = $image_manager;
    }

    /**
     * @return string
     */
    public function getShape() {
        return $this->shape;
    }

    /**
     * @param string $shape
     */
    public function setShape($shape) {
        $this->shape = $shape;
    }

    /**
     * @return int
     */
    public function getSize() {
        return $this->size;
    }

    /**
     * @param int $size
     */
    public function setSize($size) {
        $this->size = $size;
    }

    /**
     * @param string $font
     */
    public function setFont($font) {
        $this->font = $font;
    }

    /**
     * @return \Intervention\Image\Image
     */
    public function generate() {
        $words = $this->break_words($this->name);

        $number_of_word = 1;
        $this->name_initials = '';
        foreach ($words as $word) {

            if ($number_of_word > 2)
                break;

            $this->name_initials .= mb_strtoupper(trim(mb_substr($word, 0, 1, 'UTF-8')));

            $number_of_word++;
        }

        $this->color = $color = $this->stringToColor($this->name);

        if ($this->shape == 'circle') {
            $canvas = $this->image_manager->canvas(480, 480);

            $canvas->circle(480, 240, 240, function ($draw) use ($color) {
                $draw->background($color);
            });

        } else {

            $canvas = $this->image_manager->canvas(480, 480, $color);
        }

        $canvas->text($this->name_initials, 240, 240, function ($font) {
            $font->file($this->font);
            $font->size(220);
            $font->color('#ffffff');
            $font->valign('middle');
            $font->align('center');
        });

        return $canvas->resize($this->size, $this->size);
    }

    public function saveAs($path, $mimetype = 'image/png', $quality = 90) {
        if (empty($path) || empty($mimetype) || $mimetype != "image/png" && $mimetype != 'image/jpeg') {
            return false;
        }

        return @file_put_contents($path, $this->generate()->encode($mimetype, $quality));
    }

    public function getColor() {
        return $this->color;
    }

    /**
     * Returns a lighter version of the color, you can use this as backgrounds in designs perhaps
     * @param float $lighten
     * @return string
     */
    public function getBackgroundColor($lighten = 0.5) {
        return $this->colorLuminance($this->color, $lighten);
    }

    public function __toString() {
        return (string)$this->generate()->encode('data-url');
    }

    public function break_words($name) {
        $temp_word_arr = explode(' ', $name);
        $final_word_arr = [];
        foreach ($temp_word_arr as $key => $word) {
            if ($word != "" && $word != ",") {
                $final_word_arr[] = $word;
            }
        }
        return $final_word_arr;
    }

    protected function stringToColor($string) {
        $seed = crc32($string);
        mt_srand($seed);

        $options = $this->getColorOptions();
        $options['prng'] = 'mt_rand';

        $color = RandomColor::one($options);

        mt_srand();

        return $color;
    }

//    protected function stringToColor($string) {
//        // random color
//        $rgb = substr(dechex(crc32($string)), 0, 6);
//        // make it darker
//        $darker = 2;
//        list($R16, $G16, $B16) = str_split($rgb, 2);
//        $R = sprintf("%02X", floor(hexdec($R16) / $darker));
//        $G = sprintf("%02X", floor(hexdec($G16) / $darker));
//        $B = sprintf("%02X", floor(hexdec($B16) / $darker));
//        return '#' . $R . $G . $B;
//    }

    /**
     * Adjust color luminance (@see  https://gist.github.com/stephenharris/5532899)
     * @param $hex
     * @param $percent
     * @return string
     */
    protected function colorLuminance($hex, $percent) {

        // validate hex string

        $hex = preg_replace('/[^0-9a-f]/i', '', $hex);
        $new_hex = '#';

        if (strlen($hex) < 6) {
            $hex = $hex[0] + $hex[0] + $hex[1] + $hex[1] + $hex[2] + $hex[2];
        }

        // convert to decimal and change luminosity
        for ($i = 0; $i < 3; $i++) {
            $dec = hexdec(substr($hex, $i * 2, 2));
            $dec = min(max(0, $dec + $dec * $percent), 255);
            $new_hex .= str_pad(dechex($dec), 2, 0, STR_PAD_LEFT);
        }

        return $new_hex;
    }

    /**
     * @return array
     */
    public function getColorOptions() {
        return $this->colorOptions;
    }

    /**
     * @param array $colorOptions
     */
    public function setColorOptions($colorOptions) {
        $this->colorOptions = $colorOptions;
    }

}
