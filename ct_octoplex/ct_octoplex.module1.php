<?php

function ftp_get_recursive_paths($conn, $path, $max_level = 0) {
    $files = array();
    if($max_level < 0) return $files;
    if($path !== '/' && $path[strlen($path) - 1] !== '/') $path .= '/';
    $files_list = ftp_nlist($conn, $path);

    foreach($files_list as $f){
        if($f !== '.' && $f !== '..' && $f !== $path){
            if(strpos($f, '.') === FALSE){
                $files[$f] = ftp_get_recursive_paths($conn, $f, $max_level);
            }else{
                $files[] = basename($f);
            }
        }
    }

    return $files;
}

function octoplex_update() {

  global $user;

  $ftp_host = variable_get('ftp_host', NULL);
  $ftp_user = variable_get('ftp_user', NULL);
  $ftp_pwd = variable_get('ftp_pwd', NULL);

  $connection = ftp_connect($ftp_host);

  if(ftp_login($connection, $ftp_user, $ftp_pwd)){

    ftp_pasv($connection, TRUE);
    $file_listing = ftp_get_recursive_paths($connection, '/Carling Technologies', 2);

    foreach ($file_listing as $series_key => $series) {

      $series_name = trim($series_key, "/");
      $series_alias = "octoplex-series/" . strtolower($series_name);
      $series_path = drupal_lookup_path("source", $series_alias);
      $series_node = menu_get_object("node", 1, $series_path);

      if (!isset($series_node)) {
        $series_node = new stdClass();
        $series_node->type = "octoplex_series";
        node_object_prepare($series_node);
        $series_node->language = LANGUAGE_NONE;
        $series_node->uid = $user->uid;
        $series_node->status = 1;
        $series_node->promote = 0;
        $series_node->comment = 0;
      }

      $series_node->title = $series_name;
      $series_node->field_octoplex_category['und'] = null;

      foreach($series as $category_key => $category) {

        $category_name = trim(str_replace($series_name, "", $category_key), "/");
        $category_alias = "octoplex-series/" . strtolower($series_name) . "/category/" . strtolower($category_name);
        $category_path = drupal_lookup_path("source", $category_alias);
        $category_node = menu_get_object("node", 1, $category_path);

        if (!isset($category_node)) {
          $category_node = new stdClass();
          $category_node->type = "octoplex_category";
          node_object_prepare($category_node);
          $category_node->language = LANGUAGE_NONE;
          $category_node->uid = $user->uid;
          $category_node->status = 1;
          $category_node->promote = 0;
          $category_node->comment = 0;
        }

        $category_node->title = $category_name;
        $category_node->field_octoplex_product['und'] = null;

        $product_index = 0;

        foreach($category as $product_key => $product) {

          $product_name = trim(str_replace($series_key, "", $product_key), "/");
          $product_name = trim(str_replace($category_key, "", $product_key), "/");

          foreach($product as $resource_file) {

            $file_path = 'ftp://' . $ftp_user . ':' . $ftp_pwd . '@' . $ftp_host . '/' . $series_name . "/" . $category_name . "/" . $product_name . "/" . $resource_file;

            if (strpos($resource_file, ".exe") > -1) {
              $exe_path = $series_name . "/" . $category_name . "/" . $product_name . "/" . $resource_file;
            }

            if (strpos($resource_file, "_desc.txt") > -1) {

              $product_description = file_get_contents($file_path);
            }

            if (strpos($resource_file, "_meta.txt") > -1) {

               if (($handle = fopen($file_path, "r")) !== FALSE) {
                $i = 0;
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

                  if ($i == 0) {
                    $i++;
                    continue;
                  }

                  $category_node->field_octoplex_product['und'][$product_index]['field_title']['und'][0]['value'] = $data[0];
                  $category_node->field_octoplex_product['und'][$product_index]['field_version']['und'][0]['value'] = $data[1];
                  $category_node->field_octoplex_product['und'][$product_index]['field_size']['und'][0]['value'] = $data[2];
                  $category_node->field_octoplex_product['und'][$product_index]['field_date']['und'][0]['value'] = $data[3];
                  $category_node->field_octoplex_product['und'][$product_index]['field_notes']['und'][0]['value'] = $product_description;
                }
              }
            }
          }

          $product_index++;
        }

        $category_node->path['alias'] = "octoplex-series/" . strtolower($series_name) . "/category/" . strtolower($category_name);
        node_save($category_node);
        $category_nid = $category_node->nid;

        $series_node->field_octoplex_category['und'][]['nid'] = $category_nid;
      }

      $series_node->path['alias'] = "octoplex-series/" . strtolower($series_name);
      node_save($series_node);
    }

    ftp_close($connection);
  }
}

function ct_octoplex_cron () {
  octoplex_update();
}
