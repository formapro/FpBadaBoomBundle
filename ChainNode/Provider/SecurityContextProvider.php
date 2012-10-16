<?php
namespace Fp\BadaBoomBundle\ChainNode\Provider;

use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\User\UserInterface;

use BadaBoom\ChainNode\Provider\AbstractProvider;
use BadaBoom\Context;

class SecurityContextProvider extends AbstractProvider
{
    protected $securityContext;

    protected $sectionName;

    public function __construct(SecurityContextInterface $securityContext = null, $sectionName = 'security')
    {
        $this->securityContext = $securityContext;

        $this->sectionName = $sectionName;
    }

    public function handle(Context $context)
    {
        if ($this->securityContext && $token = $this->securityContext->getToken()) {
            $user = $token->getUser();
            if (is_string($user)) {
                $context->setVar($this->sectionName, array(
                    'user' => $user
                ));
            } else if ($user instanceof UserInterface) {
                $context->setVar($this->sectionName, array(
                    'user' => $user->getUsername()
                ));
            }
        }

        $this->handleNextNode($context);
    }
}