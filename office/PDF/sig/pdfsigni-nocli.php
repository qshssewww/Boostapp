<?php
/*
    This file is part of SAPP

    Simple and Agnostic PDF Parser (SAPP) - Parse PDF documents in PHP (and update them)
    Copyright (C) 2020 - Carlos de Alfonso (caralla76@gmail.com)

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Lesser General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU Lesser General Public License
    along with this program.  If not, see <https://www.gnu.org/licenses/>.
*/

use ddn\sapp\PDFDoc;

require_once('vendor/autoload.php');


        $password = trim($password);
        $argv[1] = '../pdfs/'.$file_name;

        $file_content = file_get_contents($argv[1]);
        $obj = PDFDoc::from_string($file_content);
        $argv[2] = 'client-identity.p12';
        $argv[3] = 'signed.pdf';
       
            $position = [ ];
            $image = $argv[2];
            $imagesize = @getimagesize($image);
            if ($imagesize === false) {
                fwrite(STDERR, "failed to open the image $image");
                return;
            }
            $pagesize = $obj->get_page_size(0);
            if ($pagesize === false)
                return p_error("failed to get page size");

            $pagesize = explode(" ", $pagesize[0]->val());
            // Calculate the position of the image according to its size and the size of the page;
            //   the idea is to keep the aspect ratio and center the image in the page with a size
            //   of 1/3 of the size of the page.
            $p_x = intval("". $pagesize[0]);
            $p_y = intval("". $pagesize[1]);
            $p_w = intval("". $pagesize[2]) - $p_x;
            $p_h = intval("". $pagesize[3]) - $p_y;
            $i_w = $imagesize[0];
            $i_h = $imagesize[1];

            $ratio_x = $p_w / $i_w;
            $ratio_y = $p_h / $i_h;
            $ratio = min($ratio_x, $ratio_y);

            $i_w = ($i_w * $ratio) / 3;
            $i_h = ($i_h * $ratio) / 3;
            $p_x = $p_w / 3;
            $p_y = $p_h / 3;

            // Set the image appearance and the certificate file
            $obj->set_signature_appearance(0, [ $p_x, $p_y, $p_x + $i_w, $p_y + $i_h ], $image);
            if (!$obj->set_signature_certificate($argv[3], $password)) {
                fwrite(STDERR, "the certificate is not valid");
            } else {
                $docsigned = $obj->to_pdf_file_s();
                if ($docsigned === false)
                    fwrite(STDERR, "could not sign the document");
                else
                    echo $docsigned;
            }
       
?>
