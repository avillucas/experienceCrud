<?php

namespace ExperienceCrud\Core\Domain;

class Experience {
    private $id;
    private $title;
    private $shortDescription;
    private $fullDescription;
    private $featuredImageId;
    private $email;
    private $bookingUrl;

    public function __construct(
        $id,
        string $title,
        string $shortDescription,
        string $fullDescription = '',
        int $featuredImageId = 0,
        string $email = 'turismo@catenazapata.com',
        string $bookingUrl = 'https://catenazapata.meitre.com/'
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->shortDescription = $shortDescription;
        $this->fullDescription = $fullDescription;
        $this->featuredImageId = $featuredImageId;
        $this->email = $email;
        $this->bookingUrl = $bookingUrl;
    }

    public function getId() { return $this->id; }
    public function getTitle(): string { return $this->title; }
    public function getShortDescription(): string { return $this->shortDescription; }
    public function getFullDescription(): string { return $this->fullDescription; }
    public function getFeaturedImageId(): int { return $this->featuredImageId; }
    public function getEmail(): string { return $this->email; }
    public function getBookingUrl(): string { return $this->bookingUrl; }
}
