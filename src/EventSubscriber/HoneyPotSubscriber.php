<?php

namespace App\EventSubscriber;

use Psr\Log\LoggerInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Event\PreSubmitEvent;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class HoneyPotSubscriber implements EventSubscriberInterface
{
    private LoggerInterface $honeyPotLogger;

    private RequestStack $requestStack;

    public function __construct(
        LoggerInterface $honeyPotLogger,
        RequestStack $requestStack
    )
    {
        $this->honeyPotLogger = $honeyPotLogger;
        $this->requestStack = $requestStack;
    }

    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SUBMIT => 'checkHoneyJar'
        ];
    }

    public function checkHoneyJar(PreSubmitEvent $event): void
    {
        $request = $this->requestStack->getCurrentRequest();

        if(!$request){
            return;
        }

        $data = $event->getData();

      if(!array_key_exists('phone', $data) || !array_key_exists('mail', $data))
      {
        throw new HttpException(400, "Vous ne devriez pas faire ça...");
      }

      [
        'phone' => $phone,
        'mail' => $mail
      ] = $data;

      if($phone !== "" || $mail !== ""){
        $this->honeyPotLogger->info("Bot spammeur potentiellement detecté. IP : '{$request->getClientIp()}'. Le champs phone contenait : '{$phone}' et le champs mail contenait '{$mail}'.");

        throw new HttpException(403, "Les robots ne sont pas les bienvenus ici !");
      }
    }
     
}