<?php

namespace AppBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Translation\TranslatorInterface;

class PostType extends AbstractType
{
    /** @var TranslatorInterface */
    protected $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', TextType::class)
            ->add('slug', TextType::class)
            ->add('text', TextareaType::class, [
                'attr' => [
                    'rows' => 10,
                ],
            ])
            ->add('save', SubmitType::class, array('label' => $this->translator->trans('post.label.save')));
    }

    public function getBlockPrefix()
    {
        return 'app_form_post';
    }
}