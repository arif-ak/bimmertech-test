<?php

namespace AppBundle\Entity;


use DateTime;

interface MediaFolderInterface
{

    /**
     * @return int
     */
    public function getId(): int;

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @param string $name
     */
    public function setName(string $name): void;

    /**
     * @return int
     */
    public function getImagesCount(): int;

    public function setImagesCount(): void;

    /**
     * @return int
     */
    public function getFoldersCount(): int;

    public function setFoldersCount(): void;

    /**
     * @return DateTime
     */
    public function getCreatedAt(): DateTime;

    /**
     * @param DateTime $createdAt
     */
    public function setCreatedAt(DateTime $createdAt): void;

    /**
     * @return MediaFolder
     */
    public function getParent(): ?MediaFolder;

    /**
     * @param MediaFolder $parent
     */
    public function setParent(MediaFolder $parent): void;

    /**
     * @return ArrayCollection
     */
    public function getChildren();

    /**
     * {@inheritdoc}
     */
    public function hasChild(MediaFolderInterface $mediaFolder): bool;

    /**
     * {@inheritdoc}
     */
    public function hasChildren(): bool;

    /**
     * {@inheritdoc}
     */
    public function addChild(MediaFolderInterface $mediaFolder): void;

    /**
     * {@inheritdoc}
     */
    public function removeChild(MediaFolderInterface $mediaFolder): void;
}