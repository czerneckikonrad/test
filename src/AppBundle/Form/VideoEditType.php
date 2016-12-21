<?php

namespace AppBundle\Form;

use AppBundle\Entity\PlaylistVideo;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class VideoEditType extends AbstractType
{

    public function __construct(EntityManager $entityManager) {
        $this->em = $entityManager;
    }
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('videoid', null, array('label' => 'Youtube ID'))
            ->add('title', null, array('label' => 'Nazwa'))
            ->add('description',null, array('label' => 'Opis'))
            ->add('playlist', EntityType::class, array(
                'class' => 'AppBundle:Playlist',
                'choice_label' => 'playlisttitle',
                'multiple'     => true,
                'expanded'     => true,
                'required' => false,
                'mapped' => false
            ))

        ->add('Wykonaj', SubmitType::class)
            ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Videos'
        ));
    }
}
