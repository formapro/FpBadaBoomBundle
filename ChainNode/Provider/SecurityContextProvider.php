<?php
namespace Fp\BadaBoomBundle\ChainNode\Provider;

use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\User\UserInterface;

use BadaBoom\ChainNode\Provider\AbstractProvider;
use BadaBoom\DataHolder\DataHolderInterface;


class SecurityContextProvider extends AbstractProvider
{
    protected $securityContext;

    protected $sectionName;

    public function __construct(SecurityContextInterface $securityContext, $sectionName = 'security')
    {
        $this->securityContext = $securityContext;

        $this->sectionName = $sectionName;
    }

    public function handle(\Exception $exception, DataHolderInterface $data)
    {
        if ($token = $this->securityContext->getToken()) {
            $user = $token->getUser();
            if (is_string($user)) {
                $data->set($this->sectionName, array(
                    'user' => $user
                ));
            } else if ($user instanceof UserInterface) {
                $data->set($this->sectionName, array(
                    'user' => $user->getUsername()
                ));
            }
        }

        $this->handleNextNode($exception, $data);
    }
}