<?php

namespace ExperienceCrud\Core\Domain;

class Experience {
    private $id;
    private $title;
    private $shortDescription;
    private $fullDescription;
    private $featuredImageId;
    private $includes; // Array of ['text' => string, 'url' => string]
    private $tastings; // Array of ['text' => string, 'url' => string]
    private $minPersons;
    private $maxPersons;
    private $requirements; // Array of strings
    private $email;
    private $bookingUrl;
    private $duration; // In minutes

    public function __construct(
        $id,
        string $title,
        string $shortDescription,
        string $fullDescription = '',
        int $featuredImageId = 0,
        array $includes = [],
        array $tastings = [],
        int $minPersons = 1,
        int $maxPersons = 10,
        array $requirements = [],
        string $email = 'turismo@catenazapata.com',
        string $bookingUrl = 'https://catenazapata.meitre.com/',
        int $duration = 60
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->shortDescription = $shortDescription;
        $this->fullDescription = $fullDescription;
        $this->featuredImageId = $featuredImageId;
        $this->includes = $includes;
        $this->tastings = $tastings;
        $this->minPersons = $minPersons;
        $this->maxPersons = $maxPersons;
        $this->requirements = $requirements;
        $this->email = $email;
        $this->bookingUrl = $bookingUrl;
        $this->duration = $duration;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getTitle(): string { return $this->title; }
    public function getShortDescription(): string { return $this->shortDescription; }
    public function getFullDescription(): string { return $this->fullDescription; }
    public function getFeaturedImageId(): int { return $this->featuredImageId; }
    public function getIncludes(): array { return $this->includes; }
    public function getTastings(): array { return $this->tastings; }
    public function getMinPersons(): int { return $this->minPersons; }
    public function getMaxPersons(): int { return $this->maxPersons; }
    public function getRequirements(): array { return $this->requirements; }
    public function getEmail(): string { return $this->email; }
    public function getBookingUrl(): string { return $this->bookingUrl; }
    public function getDuration(): int { return $this->duration; }
}
