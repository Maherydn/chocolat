<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{

    public function __construct(private FormListenerFactory $formListenerFactory)
    {
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('content')
            ->add('price', IntegerType::class, [
                'label' => 'Price en ar',
            ])
            ->add('quantity')
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'expanded' => true,
                'required' => false
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Enregistrer',
            ])
            ->addEventListener(FormEvents::POST_SUBMIT, $this->formListenerFactory->timestamps());
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
