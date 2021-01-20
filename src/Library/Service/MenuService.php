<?php

namespace App\Library\Service;

use App\Entity\User;
use App\Library\Model\MenuItem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;

class MenuService
{
    private $security;
    private $requestStack;

    public function __construct(Security $security, RequestStack $requestStack)
    {
        $this->security = $security;
        $this->requestStack = $requestStack;
    }

    /**
     * @return MenuItem[]
     * @throws \Exception
     */
    public function getMenu(): array
    {
        $config = $this->getMenuConfig();
        $menu = [];

        foreach ($config as $menuItem) {
            $role = $menuItem->getRole();
            if ($this->security->isGranted($role)) {
                $menu[] = $menuItem;
            }
        }

        return $menu;
    }

    /**
     * @return MenuItem[]
     * @throws \Exception
     */
    private function getMenuConfig(): array
    {
        $landingItem = new MenuItem(
            'Inicio',
            $this->isActive('landing'),
            'landing_index',
            'ROLE_USER',
            'icons/home.svg'
        );
        $taskItem = new MenuItem(
            'Semanal',
            $this->isActive('task'),
            'task_index',
            'ROLE_TASK',
            'icons/calendar-week.svg'
        );
        $transactionItem = new MenuItem(
            'Monedero',
            $this->isActive('transaction'),
            'transactioncategory_index',
            'ROLE_TRANSACTION',
            'icons/wallet.svg'
        );

        return [$landingItem, $taskItem, $transactionItem];
    }

    /**
     * @param string $entity
     * @return bool
     * @throws \Exception
     */
    private function isActive(string $entity): bool
    {
        return strpos($this->getEntityFromRouteName(), $entity) !== false;
    }

    /**
     * @return string
     * @throws \Exception
     */
    private function getEntityFromRouteName(): string
    {
        $request = $this->requestStack->getCurrentRequest();
        if (!$request instanceof Request) {
            throw new \Exception('Not request found');
        }

        $route = $request->attributes->get('_route');
        return explode($route, '_')[0];
    }

    /**
     * @return User
     */
    private function getUser(): User
    {
        $user = $this->security->getUser();
        if (!$user instanceof User) {
            throw new AccessDeniedException();
        }

        return $user;
    }
}
