<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 7/7/2015
 * Time: 2:51 PM
 */

namespace Tops\cms\drupal7;


class TaxonomyManager {
    private $vocabularies;

    function __construct($basePath) {
        $vocabularies =  taxonomy_vocabulary_get_names();
        $this->vocabularies = array();
        foreach ($vocabularies as $v) {
            $this->vocabularies[$v->name] = $v->machine_name;

        }
    }

    private function getTaxonomyTerm($vocabulary,$termName) {
        if (empty($termName)) {
            return null;
        }
        $terms = taxonomy_get_term_by_name($termName,$this->vocabularies[$vocabulary]);
        if (empty($terms)) {
            return null;
        }
        $term = array_values($terms)[0];
        return $term;

    }

    public function updateAnnualSessionTerms() {
        $weight = 1;
        for ($y=2020;$y >1979;$y--) {
            $term = $this->getTaxonomyTerm('Annual sessions',$y);
            if (empty($term)) {
                $term = new \stdClass();
                $term->vid = 11;
                $term->name = $y;
            }
            $term->weight = $weight++;
            $result = taxonomy_term_save($term);
            if ($result == SAVED_NEW) {
                print("Created: $y\n");
            }
            else if ($result == SAVED_UPDATED) {
                print("Updated: $y\n");
            }
            else {
                print("UPDATE FAILED: $y\n");
            }
        }

    }

}

