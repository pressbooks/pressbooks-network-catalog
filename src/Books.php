<?php

namespace  PressbooksNetworkCatalog;

use Pressbooks\Contributors;
use Pressbooks\DataCollector\Book;
use Pressbooks\Metadata;

class Books
{
    public function get(array $params = []): array
    {
        $this->rawBooksList = $this->query($params);
        $this->booksList = $this->prepareBooksList($this->rawBooksList);
        return $this->booksList;
    }

    /**
     * @param array $params
     * @return array
     */
    private function query( array $params = [] )
    {
        global $wpdb;

        $cover_image = Book::COVER;
        $title = Book::TITLE;
        $url = Book::BOOK_URL;
        $language = Book::LANGUAGE;
        $last_edited = Book::LAST_EDITED;
        $subject = Book::SUBJECT;
        $license = Book::LICENSE;
        $h5p_activities = Book::H5P_ACTIVITIES;
        $in_catalog = Book::IN_CATALOG;
        $information_array = Book::BOOK_INFORMATION_ARRAY;
        $sql_query = "SELECT SQL_CALC_FOUND_ROWS
            b.blog_id AS id,
            MAX(IF(b.meta_key=%s,b.meta_value,null)) AS cover,
            MAX(IF(b.meta_key=%s,b.meta_value,null)) AS title,
            MAX(IF(b.meta_key=%s,b.meta_value,null)) AS url,
            MAX(IF(b.meta_key=%s,b.meta_value,null)) AS information_array,
            MAX(IF(b.meta_key=%s,CAST(b.meta_value AS DATETIME),null)) AS updated_at,
            MAX(IF(b.meta_key=%s,b.meta_value,null)) AS language,
            MAX(IF(b.meta_key=%s,b.meta_value,null)) AS subjects,
            MAX(IF(b.meta_key=%s,b.meta_value,null)) AS license,
            MAX(IF(b.meta_key=%s,CAST(b.meta_value AS UNSIGNED),null)) AS h5p_count
        FROM {$wpdb->blogmeta} b
        WHERE blog_id IN (
            SELECT blog_id FROM {$wpdb->blogmeta}
                WHERE meta_key = %s AND meta_value = '1'
            )
        GROUP BY blog_id";
        return $wpdb->get_results(
            $wpdb->prepare(
                $sql_query,
                $cover_image,
                $title,
                $url,
                $information_array,
                $last_edited,
                $language,
                $subject,
                $license,
                $h5p_activities,
                $in_catalog
            ),
            ARRAY_A
        );
    }

    private function prepareBooksList(array $booksList): array
    {
        foreach ($booksList as &$book) {
            $book_information = unserialize($book['information_array']);
            $book['authors'] = isset($book_information['pb_authors']) ? $book_information['pb_authors'] :  '';
            $book['editors'] = isset($book_information['pb_editors']) ? $book_information['pb_editors'] :  '';
            $book['description'] = isset($book_information['pb_about_50']) ? $book_information['pb_about_50'] :  '';
            $book['institutions'] = isset($book_information['pb_institutions']) ?
                implode(',', $book_information['pb_institutions']) : '';
            $book['publisher'] = isset($book_information['pb_publisher']) ? $book_information['pb_publisher'] :  '';
        }
        return $booksList;
    }
}
