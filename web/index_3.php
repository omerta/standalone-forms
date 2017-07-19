<?php
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use Symfony\Bridge\Twig\Extension\FormExtension;
use Symfony\Bridge\Twig\Form\TwigRenderer;
use Symfony\Bridge\Twig\Form\TwigRendererEngine;

use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\Loader\XliffFileLoader;
use Symfony\Bridge\Twig\Extension\TranslationExtension;

require __DIR__.'/../vendor/autoload.php';

$vendorTwigBridgeDir = __DIR__.'/../vendor/symfony/twig-bridge/Resources/views/Form';
$defaultFormTheme = 'bootstrap_3_horizontal_layout.html.twig';

$templates = array(__DIR__.'/../views/',
                    $vendorTwigBridgeDir);
$loader = new Twig_Loader_Filesystem($templates);
$twig = new Twig_Environment($loader);

/* 3.1 < */
// $formEngine = new TwigRendererEngine(array($defaultFormTheme));
// $formEngine->setEnvironment($twig);
//
// $twig->addExtension(
//     new FormExtension(new TwigRenderer($formEngine))
// );

/* 3.3 */
// https://github.com/symfony/symfony/issues/21008
$formEngine = new TwigRendererEngine(array($defaultFormTheme), $twig);
$twig->addRuntimeLoader(new \Twig_FactoryRuntimeLoader(array(
    TwigRenderer::class => function () use ($formEngine, $csrfManager) {
        return new TwigRenderer($formEngine, $csrfManager);
    },
)));
$twig->addExtension(new FormExtension());

$translator = new Translator('en');
$translator->addLoader('xlf', new XliffFileLoader());
// vendor/symfony/form/Resources/translations/validators.en.xlf
$translator->addResource('xlf',
                         __DIR__.'/../vendor/symfony/form/Resources/translations/validators.en.xlf'
                         ,'en');

$twig->addExtension(new TranslationExtension($translator));

$formFactory = Forms::createFormFactory();
$form = $formFactory->createBuilder()
    ->add('primerNombre', TextType::class)
    ->add('segundoNombre', TextType::class)
    ->add('primerApellido', TextType::class)
    ->getForm();

echo $twig->render('index.html.twig', array('form' => $form->createView()));
