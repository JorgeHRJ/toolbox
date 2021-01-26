<?php

namespace App\Twig\Extension;

use App\Entity\User;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class UserExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('get_status_data', [$this, 'getStatusData']),
            new TwigFunction('get_roles_labels', [$this, 'getRolesLabels'])
        ];
    }

    /**
     * @param User $user
     * @return array
     * @throws \Exception
     */
    public function getStatusData(User $user): array
    {
        switch ($user->getStatus()) {
            case User::DISABLED_STATUS:
                return ['label' => 'Deshabilitado', 'class' => 'danger'];
            case User::ENABLED_STATUS:
                return ['label' => 'Habilitado', 'class' => 'success'];
            default:
                throw new \Exception(sprintf('Status %s not handled', $user->getStatus()));
        }
    }

    /**
     * @param User $user
     * @return string
     */
    public function getRolesLabels(User $user): string
    {
        return implode(', ', $user->getRoles());
    }
}
