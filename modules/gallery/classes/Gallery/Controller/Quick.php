<?php defined("SYSPATH") or die("No direct script access.");
/**
 * Gallery - a web based photo album viewer and editor
 * Copyright (C) 2000-2013 Bharat Mediratta
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or (at
 * your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street - Fifth Floor, Boston, MA  02110-1301, USA.
 */
class Gallery_Controller_Quick extends Controller {
  public function action_rotate() {
    $id = $this->request->arg(0, "digit");
    $dir = $this->request->arg(1, "alpha");
    Access::verify_csrf();
    $item = ORM::factory("Item", $id);
    Access::required("view", $item);
    Access::required("edit", $item);

    $degrees = 0;
    switch($dir) {
    case "ccw":
      $degrees = -90;
      break;

    case "cw":
      $degrees = 90;
      break;
    }

    if ($degrees) {
      $tmpfile = System::temp_filename("rotate",
        pathinfo($item->file_path(), PATHINFO_EXTENSION));
      GalleryGraphics::rotate($item->file_path(), $tmpfile, array("degrees" => $degrees), $item);
      $item->set_data_file($tmpfile);
      $item->save();
    }

    if ($this->request->query("page_type") == "collection") {
      $this->response->json(
        array("src" => $item->thumb_url(),
              "width" => $item->thumb_width,
              "height" => $item->thumb_height));
    } else {
      $this->response->json(
        array("src" => $item->resize_url(),
              "width" => $item->resize_width,
              "height" => $item->resize_height));
    }
  }
}
