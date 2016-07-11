<?php
namespace AppBundle\Services\Security;

use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider as BaseClass;
use Symfony\Component\Security\Core\User\UserInterface;

class FOSUBUserProvider extends BaseClass
{

    /**
     * {@inheritDoc}
     */
    public function connect(UserInterface $user, UserResponseInterface $response)
    {
        $property = $this->getProperty($response);
        $username = $response->getUsername();

        //on connect - get the access token and the user ID
        $service = $response->getResourceOwner()->getName();

        $setter = 'set'.ucfirst($service);
        $setter_id = $setter.'Id';
        $setter_token = $setter.'AccessToken';

        //we "disconnect" previously connected users
        if (null !== $previousUser = $this->userManager->findUserBy(array($property => $username))) {
            $previousUser->$setter_id(null);
            $previousUser->$setter_token(null);
            $this->userManager->updateUser($previousUser);
        }

        //we connect current user
        $user->$setter_id($username);
        $user->$setter_token($response->getAccessToken());

        $this->userManager->updateUser($user);
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        $username = $response->getUsername();
        $user = $this->userManager->findUserBy(array($this->getProperty($response) => $username));

        //when the user is registrating
        if (null === $user) {
            $email = $response->getEmail();
            if(filter_var($email, FILTER_VALIDATE_EMAIL) === false)
                $email = $username.'@entrelectores.com';

            $user = $this->userManager->findUserBy(array('email' => $email));
            // create new user here
            if(is_null($user)){
                //check if username is gotten
                $nickExists = $this->userManager->findUserBy(array('username' => $response->getNickName()));

                if(is_null($nickExists)){
                    $nick = $response->getNickName();
                } else {
                    $nick = $response->getNickName().'-'.uniqid();
                }

                $user = $this->userManager->createUser();
                $user->setUsername($nick);
                $user->setName($response->getRealName());
                $user->setSlug($user->getUsername());
                $user->setEmail($email);
                $user->setPlainPassword($email.uniqid());
                $user->setEnabled(true);
                $user->addRole('ROLE_USER');
            }

            $service = $response->getResourceOwner()->getName();
            $setter = 'set'.ucfirst($service);
            $setter_id = $setter.'Id';
            $setter_token = $setter.'AccessToken';

            $user->$setter_id($username);
            $user->$setter_token($response->getAccessToken());
            $this->userManager->updateUser($user);
            return $user;
        }

        //if user exists - go with the HWIOAuth way
        $user = parent::loadUserByOAuthUserResponse($response);

        $serviceName = $response->getResourceOwner()->getName();
        $setter = 'set' . ucfirst($serviceName) . 'AccessToken';

        //update access token
        $user->$setter($response->getAccessToken());

        return $user;
    }

}
