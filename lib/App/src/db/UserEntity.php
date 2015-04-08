<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 12/28/2014
 * Time: 7:42 AM
 */

namespace App\db;

/**
 * @Entity
 * @Table(name="test")
 */
class UserEntity {
    /**
     * @Id @Column(type="integer")
     * @GeneratedVAlue
     */
    private $id;

    public function getId() {
        return $this->id;
    }

    /**
     * @var string
     *
     * @Column(name="name", type="string", length=50, nullable=true)
     */
    private $name;

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }


}