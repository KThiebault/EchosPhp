<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Tag;
use App\Type\State;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class BookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, ['label' => 'Title:'])
            ->add('state', EnumType::class, [
                'label' => 'State:',
                'class' => State::class
            ])
            ->add('tags', EntityType::class, [
                'label' => 'Tags:',
                'class' => Tag::class,
                'choice_label' => fn(Tag $tag) => $tag->getName(),
                'multiple' => true,
                'expanded' => true
            ])
            ->add('summary', TextareaType::class, ['label' => 'Summary:']);
    }
}
