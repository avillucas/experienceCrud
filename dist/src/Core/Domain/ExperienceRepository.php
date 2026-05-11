<?php

namespace ExperienceCrud\Core\Domain;

interface ExperienceRepository {
    public function findAll(string $lang = ''): array;
    public function findById($id): ?Experience;
    public function save(Experience $experience): void;
}
