<?php
namespace AppBundle\Service;

use Symfony\Component\DependencyInjection\Container;
use RMS\PushNotificationsBundle\Message\iOSMessage;
use RMS\PushNotificationsBundle\Service\Notifications;
use RMS\PushNotificationsBundle\Device\Types;
use AppBundle\Entity as Entity;
class PushService
{
    protected $_container;
    
    protected $_notifications;
    
    public function __construct(Container $container, Notifications $notifications) {
        $this->_container = $container;
        
        $this->_notifications = $notifications;
    }
    
    public function getContent()
    {
        
    }
    
    public function push(array $users, $content, $sound = 'default', $badge = TRUE)
    {
        foreach ($users as $u) {
            
            if (!$u->getDeviceToken()) {
                continue;
            }
            //TODO, check device token to get device type
            $type = Types::OS_IOS;
            
            switch($type) {
                case Types::OS_IOS: {
                
                    $message = new iOSMessage();
                    $message->setAPSSound($sound);
                    if($badge) {
                        $message->setAPSBadge((int) $u->getBadge() + 1);
                        $this->incrementBadge($u);
                    }
                    break;
                }
                case Types::OS_ANDROID_C2DM: {
                    break;
                }
                case Types::OS_ANDROID_GCM: {
                    break;
                }
                case Types::OS_MAC: {
                    break;
                }
                case Types::OS_BLACKBERRY: {
                    break;
                }
                case Types::OS_WINDOWSMOBILE: {
                    break;
                }
                case Types::OS_WINDOWSPHONE: {
                    break;
                }
                default: {
                    continue 2;
                }
            }

            $message->setDeviceIdentifier($u->getDeviceToken());
            $message->setMessage($content);
            $this->_notifications->queue($message);
        }
        $this->_notifications->flush();

    }
    
    public function incrementBadge(Entity\User $user)
    {
        $user->setBadge($user->getBadge() + 1);
        $em = $this->_container->get('doctrine')->getManager();
        $em->persist($user);
        $em->flush();
    }
}