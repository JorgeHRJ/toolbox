<?php

namespace App\Library\Model;

class MenuItem
{
    /** @var string */
    private $title;

    /** @var bool */
    private $isActive;

    /** @var string */
    private $routeName;

    /** @var string */
    private $role;

    /** @var string */
    private $icon;

    /** @var MenuItem[] */
    private $submenus;

    public function __construct(
        string $title,
        bool $isActive,
        string $routeName,
        string $role,
        string $icon,
        array $submenus = []
    ) {
        $this->isActive = $isActive;
        $this->routeName = $routeName;
        $this->role = $role;
        $this->icon = $icon;
        $this->title = $title;
        $this->submenus = $submenus;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->isActive;
    }

    /**
     * @param bool $isActive
     */
    public function setIsActive($isActive): void
    {
        $this->isActive = $isActive;
    }

    /**
     * @return string
     */
    public function getRouteName(): string
    {
        return $this->routeName;
    }

    /**
     * @param string $routeName
     */
    public function setRouteName(string $routeName): void
    {
        $this->routeName = $routeName;
    }

    /**
     * @return string
     */
    public function getRole(): string
    {
        return $this->role;
    }

    /**
     * @param string $role
     */
    public function setRole(string $role): void
    {
        $this->role = $role;
    }

    /**
     * @return string
     */
    public function getIcon(): string
    {
        return $this->icon;
    }

    /**
     * @param string $icon
     */
    public function setIcon(string $icon): void
    {
        $this->icon = $icon;
    }

    /**
     * @return MenuItem[]
     */
    public function getSubmenus(): array
    {
        return $this->submenus;
    }

    /**
     * @param MenuItem[] $submenus
     */
    public function setSubmenus(array $submenus): void
    {
        $this->submenus = $submenus;
    }
}
