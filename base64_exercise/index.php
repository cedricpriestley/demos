<?php

$url = "https://raw.githubusercontent.com/fourkitchens/careers/master/exercises/base64/index.html";
$html = file_get_contents($url);
convert_src_tags($html);
file_put_contents("index.html", $html);
print $html;

// Convert image sources to base64 data URIs
function convert_src_tags(&$html) {

  $html_doc = new DOMDocument();
  @$html_doc->loadHTML($html);

  $tags = $html_doc->getElementsByTagName('img');

  foreach ($tags as $tag) {
    $img_src = $tag->getAttribute('src');
    $img_src = "https://github.com/fourkitchens/careers/blob/master/exercises/base64" . $img_src . "?raw=true";

    $image_data = file_get_contents($img_src);
    $image_type = pathinfo($img_src, PATHINFO_EXTENSION);
    $new_img_src = 'data:image/' . $image_type . ';base64,' . base64_encode($image_data);

    $tag->setAttribute('src', $new_img_src);

    $html = $html_doc->saveHTML();
  }

  /*
  $tags = $html_doc->getElementsByTagName('script');

  // prepend base url to javascript sources
  foreach ($tags as $tag) {
    $js_src = $tag->getAttribute('src');
    $js_src = "https://raw.githubusercontent.com/fourkitchens/careers/master/exercises/base64" . $js_src;

    $tag->setAttribute('src', $js_src);

    $html = $html_doc->saveHTML();
  }
  */
}

