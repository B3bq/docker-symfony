<?php
namespace App\Form\Type;

use App\Repository\UsersRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class TaskType extends AbstractType
{
    public function __construct(
        private UsersRepository $usersRepository,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // use $this->categoryRepository to access the repository
    }
}