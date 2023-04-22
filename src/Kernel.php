<?php

declare(strict_types=1);

namespace App;

use App\Doctrine\AbstractEnumType;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel implements CompilerPassInterface
{
    use MicroKernelTrait;

    public function process(ContainerBuilder $container): void
    {
        $taggedEnums = $container->findTaggedServiceIds('app.doctrine_enum_type');

        if ($container->hasParameter('doctrine.dbal.connection_factory.types')) {
            $typesDefinition = $container->getParameter('doctrine.dbal.connection_factory.types');
        }

        /** @var $enumType AbstractEnumType */
        foreach ($taggedEnums as $enumType => $definition) {
            $typesDefinition[$enumType::STATE] = ['class' => $enumType];
        }

        $container->setParameter('doctrine.dbal.connection_factory.types', $typesDefinition ?? []);
    }
}
