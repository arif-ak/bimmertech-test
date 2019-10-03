<?php

namespace AppBundle\Fixture;

use AppBundle\Entity\Contact;
use AppBundle\Entity\ContactContent;
use AppBundle\Entity\ContactTitle;
use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\FixturesBundle\Fixture\AbstractFixture;
use Sylius\Bundle\FixturesBundle\Fixture\FixtureInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

class ContactPageFixture extends AbstractFixture implements FixtureInterface
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * ContactTitle constructor.
     *
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * @param array $options
     */
    public function load(array $options): void
    {
        $contacts = $options['custom'];
        foreach ($contacts as $key => $item) {
            $contactTitle = $this->objectManager->getRepository(ContactTitle::class)->findOneBy(['name' => $item['title']]);

            if (null === $contactTitle) {
                $contactTitle = new ContactTitle();
            }

            //$contactTitle = new ContactTitle();
            $contactTitle->setName($item['title']);
            $contactTitle->setPopup($item['popup']);

            if (isset($item['contacts'])) {
                $this->createContact($contactTitle, $item['contacts']);
            }

            $this->objectManager->persist($contactTitle);
        }

        $this->objectManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'contact_page';
    }

    /**
     * @param ArrayNodeDefinition $resourceNode
     */
    protected function configureOptionsNode(ArrayNodeDefinition $resourceNode): void
    {
        $resourceNode
            ->children()
            ->arrayNode('custom')
            ->performNoDeepMerging()
            ->prototype('array')
            ->children()
            ->scalarNode('title')->end()
            ->scalarNode('popup')->end()
            ->arrayNode('contacts')->prototype('array')
            ->children()
            ->scalarNode('position')->end()
            ->arrayNode('contact_contents')->prototype('array')
            ->children()
            ->scalarNode('content_title')->end()
            ->scalarNode('content_value')->end()
            ->scalarNode('content_hours')->end()
            ->end();
    }

    /**
     * @param array $content
     * @return ContactContent
     */
    protected function createContactContent(array $content)
    {
        $contactContent = new ContactContent();
        $contactContent->setTitle($content['content_title']);
        $contactContent->setValue($content['content_value']);
        $contactContent->setHours($content['content_hours']);
        $this->objectManager->persist($contactContent);

        return $contactContent;
    }

    /**
     * @param ContactTitle $contactTitle
     * @param $data
     */
    public function createContact(ContactTitle $contactTitle, $data)
    {
        foreach ($data as $value) {

            $contact = $this->objectManager->getRepository(Contact::class)->findOneBy(['contactPosition' => $value['position']]);

            if (null === $contact) {
                $contact = new Contact();
            }else{
                continue;
            }

            //$contact = new Contact();
            $contact->setContactMainTitle($contactTitle);

            $contact->setContactPosition($value['position']);

            foreach ($value['contact_contents'] as $item) {
                $contact->addContactContent($this->createContactContent($item));
            }

            $this->objectManager->persist($contact);
        }

    }

}