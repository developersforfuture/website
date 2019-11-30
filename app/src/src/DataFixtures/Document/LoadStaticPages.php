<?php

namespace App\DataFixtures\PHPCR;

use App\DataFixtures\Document\DocumentFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Sulu\Bundle\PageBundle\Document\HomeDocument;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Yaml\Parser;

/**
 * @author Maximilian Berghoff <Maximilian.Berghoff@mayflower.de>
 */
class LoadStaticPages extends DocumentFixture
{

    public function getOrder()
    {
        return 10;
    }

    /**
     * @throws DocumentManagerException
     * @throws MetadataNotFoundException
     * @throws Exception
     */
    public function load(DocumentManager $documentManager)
    {
        if (!$documentManager instanceof DocumentManager) {
            $class = get_class($documentManager);

            throw new \RuntimeException("Fixture requires a PHPCR ODM DocumentManager instance, instance of '$class' given.");
        }

        if ($this->container === null) {
            throw new \Exception('Container cannot be null.');
        }


        $this->loadPages($documentManager);
        #$this->loadContactSnippet($documentManager);
        $this->loadHomepage($documentManager);

        // Needed, so that a Document use by loadHomepageGerman is managed.
        $documentManager->flush();
        #$this->updatePages($documentManager, AppFixtures::LOCALE_EN);

        $documentManager->flush();
    }

    /**
     * @return mixed[]
     * @throws MetadataNotFoundException
     *
     */
    private function loadPages(DocumentManager $documentManager): array
    {
        $yaml = new Parser();
        $configFilePath = __DIR__ . '/../../Resources/data/static_pages.yml';
        $content = file_get_contents($configFilePath);

        $pages = [];
        if ($content === false) {
            throw new \Exception("'$configFilePath' could not be read.");
        }
        $data = $yaml->parse($content);
        foreach ($data['static'] as $pageData) {
            
            $pages[$pageData['url']] = $this->createPage($documentManager, $pageData);

        }

        $pageDataList = [
            [
                'locale' => AppFixtures::LOCALE_EN,
                'title' => 'IMPRINT / LEGAL NOTICE',
                'url' => '/terms',
                'parent_path' => '/cmf/econ4future/contents',
                'subtitle' => '',
                'navigationContexts' => ['footer'],
                'article' => '<h5>Liability and information according to section 5 of the TMG (Telemediengesetz – Tele Media Act by German law):</h5><p>Initiative Economists for Future</p><p>E-Mail: <a href="mailto:info@econ4future.org"><u>info@econ4future.org</u></a></p><p>℅ Netzwerk Plurale Ökonomik e.V.</p><p>Willy-Brandt-Platz 5</p><p>69115 Heidelberg</p><p>Deutschland</p><p>UID: DE312301389</p><p>Represented by: Yannik Beermann, Elena Goschin, Andreas Jaumann, Eva Lickert, Anna Reisch, Henri Schneider. Contact: <a href="mailto:vorstand@plurale-oekonomik.de"><u>vorstand@plurale-oekonomik.de</u></a></p><p>The Network for Pluralist Economics is a non-profit association that supports this project / website infrastructure. It is committed to diversity and independence; see <a href="http://www.plurale-oekonomik.de"><u>www.plurale-oekonomik.de</u></a></p><p>Register: Amtsgericht Mannheim, Registration number: VR 333294</p><p>****</p><p>This imprint also applies to the Economists for Future appearances on <a href="https://facebook.com/econ4future"><u>Facebook</u></a>, <a href="https://twitter.com/econ4future"><u>Twitter</u></a>, <a href="https://instagram.com/economists4future"><u>Instagram</u></a> and <a href="https://www.linkedin.com/company/economists-for-future"><u>LinkedIn</u></a>.</p><h4>Web Design and Web Development:</h4><p>Benjamin Petersen, Patricia ??, Maximilian Berghoff (Developers for Future)</p><h4 style="text-align:justify;">Limitation of liability for internal content</h4><p style="text-align:justify;">The content of our website has been compiled with meticulous care and to the best of our knowledge. However, we cannot assume any liability for the up-to-dateness, completeness or accuracy of any of the pages.</p><p style="text-align:justify;">Pursuant to section 7, para. 1 of the TMG (Telemediengesetz – Tele Media Act by German law), we as service providers are liable for our own content on these pages in accordance with general laws. However, pursuant to sections 8 to 10 of the TMG, we as service providers are not under obligation to monitor external information provided or stored on our website. Once we have become aware of a specific infringement of the law, we will immediately remove the content in question. Any liability concerning this matter can only be assumed from the point in time at which the infringement becomes known to us.</p><h4 style="text-align:justify;">Limitation of liability for external links</h4><p style="text-align:justify;">Our website contains links to the websites of third parties (“external links”). As the content of these websites is not under our control, we cannot assume any liability for such external content. In all cases, the provider of information of the linked websites is liable for the content and accuracy of the information provided. At the point in time when the links were placed, no infringements of the law were recognisable to us. As soon as an infringement of the law becomes known to us, we will immediately remove the link in question.</p><h4 style="text-align:justify;">Copyright</h4><p style="text-align:justify;">The content and works published on this website are governed by the copyright laws of Germany. Any duplication, processing, distribution or any form of utilisation beyond the scope of copyright law shall require the prior written consent of the author or authors in question.</p><h4 style="text-align:justify;">Data protection</h4><p style="text-align:justify;">A visit to our website can result in the storage on our server of information about the access (date, time, page accessed). This does not represent any analysis of personal data (e.g., name, address or email address). If personal data are collected, this only occurs – to the extent possible – with the prior consent of the user of the website. Any forwarding of the data to third parties without the express consent of the user shall not take place.</p><p style="text-align:justify;">We would like to expressly point out that the transmission of data via the Internet (e.g., by email) can offer security vulnerabilities. It is therefore impossible to safeguard the data completely against access by third parties. We cannot assume any liability for damages arising as a result of such security vulnerabilities.</p><p style="text-align:justify;">The use by third parties of all published contact details for the purpose of advertising is expressly excluded. We reserve the right to take legal steps in the case of the unsolicited sending of advertising information; e.g., by means of spam mail.</p><p>You can find general information on data usage at Economists for Future at&nbsp;</p><p><a href="https://econ4future.org/privacy-policy"><u>https://econ4future.org/privacy-policy</u></a></p>',
                'structureType' => 'default',
            ], [
                'locale' => AppFixtures::LOCALE_EN,
                'title' => 'PRIVACY POLICY',
                'url' => '/privacy-policy',
                'parent_path' => '/cmf/econ4future/contents',
                'subtitle' => '',
                'article' => '<h5>Sign now</h5><p>By signing the open letter and giving us your name, your email address and your XXX, you are confirming to us your support in a global campaign that tries to raise awareness on climate change in the economics community. If opted in we’ll use your email address occasionally to send you updates on our current campaign, but we will never pass it on to anyone else. You will always have an opportunity to opt-out of email communications in any emails that we send to you.</p><p>The XXX is also important to us in order to differentiate between academic economists, students of economics and other people who are interested in supporting this open letter.&nbsp;</p><h5>Economists for Future campaign and website</h5><p>Economists for Future (“We”) are committed to protecting and respecting your privacy.</p><p>This policy (together with our terms of use <a href="https://www.econ4future.org/terms/">https://www.econ4future.org/terms/</a> and any other documents referred to on it) sets out the basis on which any personal data we collect from you, or that you provide to us, will be processed by us.&nbsp; Please read the following carefully to understand our views and practices regarding your personal data and how we will treat it. By visiting <a href="https://ww.econ4future.org">https://ww.econ4future.org</a> you are accepting and consenting to the practices described in this policy.</p><p>For the purpose of the Data Protection Act 1998 (the Act), the data controller is Netzwerk Plurale Ökonomik e.V.</p><p>&nbsp;</p><p>Netzwerk Plurale Ökonomik e.V.&nbsp;<br>E-Mail: <a href="mailto:info@econ4future.org"><u>info@econ4future.org</u></a>&nbsp;<br>Willy-Brandt-Platz 5, 69115 Heidelberg<br>Germany</p><p><br>The Network for Pluralist Economics is a non-profit association that supports this project / website infrastructure. It is committed to diversity and independence; see <a href="http://www.plurale-oekonomik.de"><u>www.plurale-oekonomik.de</u></a></p><p>We will collect and process the following data about you:</p><p>Information you give us. This is information about you that you give us by filling in forms on our site <a href="https://ww.econ4future.org">https://ww.econ4future.org</a> (our site) or by corresponding with us by e-mail or otherwise. It includes information you provide when you opt-in to receive emails for us about campaign updates, when you report a problem with our site or when you want to be actively involved in our campaign. The information you give us may include your name, e-mail address, your XXX and if you want to participate actively in this campaign.</p><p>&nbsp;</p><ul><li>Information we collect about you. With regard to each of your visits to our site we will automatically collect the following information:<ul><li>&nbsp;</li></ul></li></ul><h5>Cookies</h5><h4>Server log files</h4><p>The provider of this website and its pages automatically collects and stores information in so-called server log files, which your browser communicates to us automatically. The information comprises:</p><ul><li style="text-align:justify;">The type and version of browser used</li><li style="text-align:justify;">The used operating system</li><li style="text-align:justify;">Referrer URL</li><li style="text-align:justify;">The hostname of the accessing computer</li><li style="text-align:justify;">The time of the server inquiry</li><li style="text-align:justify;">The IP address</li></ul><p>This data is not merged with other data sources.</p><p>This data is recorded on the basis of Art. 6 Sect. 1 lit. f GDPR. The operator of the website has a legitimate interest in the technically error free depiction and the optimization of the operator’s website. In order to achieve this, server log files must be recorded.</p><h4>Uses made of the information</h4><p>By signing the open letter and giving us your name, your email address and your XXX, you are confirming to us your support in a global campaign that tries to raise awareness on climate change in the economics community. If opted in we’ll use your email address occasionally to send you updates on our current campaign, but we will never pass it on to anyone else. You will always have an opportunity to opt-out of email communications in any emails that we send to you.</p><p>The XXX is also important to us in order to differentiate between academic economists, students of economics and other people who are interested in supporting this open letter.&nbsp;</p><p>We use information held about you in the following ways:</p><ul><li>Information you give to us. We will use this information:<ul><li>&nbsp;</li></ul></li><li>Information we collect about you. We will use this information:<ul><li>&nbsp;</li></ul></li></ul><h3>Newsletter</h3><p>If you would like to subscribe to the newsletter offered on this website, we will need from you an e-mail address as well as information that allow us to verify that you are the owner of the e-mail address provided and consent to the receipt of the newsletter. No further data shall be collected or shall be collected only on a voluntary basis. We shall use such data only for the sending of the requested information and shall not share such data with any third parties.</p><p>The processing of the information entered into the newsletter subscription form shall occur exclusively on the basis of your consent (Art. 6 Sect. 1 lit. a GDPR). You may revoke the consent you have given to the archiving of data, the e-mail address and the use of this information for the sending of the newsletter at any time, for instance by clicking on the „Unsubscribe“ link in the newsletter. This shall be without prejudice to the lawfulness of any data processing transactions that have taken place to date.</p><p>The data you archive with us for the purpose of the newsletter subscription shall be archived by us until you unsubscribe from the newsletter. Once you cancel your subscription to the newsletter, the data shall be deleted. This shall not affect data we have been archiving for other purposes.</p><p>For sending out emails we use Mailtrain an open source mass mailing service. For more details, see: <a href="https://mailtrain.org/"><u>https://mailtrain.org/</u></a></p><h3>Analysis tools and tools provided by third parties</h3><p>There is a possibility that your browsing patterns will be statistically analysed when your visit our website. Such analyses are performed primarily with cookies and with what we refer to as analysis programmes. As a rule, the analyses of your browsing patterns are conducted anonymously; i.e. the browsing patterns cannot be traced back to you.</p><h3>SSL and/or TLS encryption</h3><p>For security reasons and to protect the transmission of confidential content, such as purchase orders or inquiries you submit to us as the website operator, this website uses either an SSL or a TLS encryption programme. You can recognise an encrypted connection by checking whether the address line of the browser switches from „http://“ to „https://“ and also by the appearance of the lock icon in the browser line.</p><p>If the SSL or TLS encryption is activated, data you transmit to us cannot be read by third parties.</p><h3>Where we store your personal data</h3><p>The data is stored on a german server hosted by netcup GmbH, Daimlerstraße 25, D-76185 Karlsruhe. By submitting your personal data, you agree to this transfer, storing or processing. We will take all steps reasonably necessary to ensure that your data is treated securely and in accordance with this privacy policy.</p><p>Disclosure of your information</p><p>Your rights</p><h3>Revocation of your consent to the processing of data</h3><p>A wide range of data processing transactions are possible only subject to your express consent. You can also revoke at any time any consent you have already given us. To do so, all you are required to do is sent us an informal notification via e-mail. This shall be without prejudice to the lawfulness of any data collection that occurred prior to your revocation.</p><p>Our site may, from time to time, contain links to and from the websites of our partner networks and affiliates.&nbsp; If you follow a link to any of these websites, please note that these websites have their own privacy policies and that we do not accept any responsibility or liability for these policies.&nbsp; Please check these policies before you submit any personal data to these websites.&nbsp;</p><h3>Access to information</h3><p>The Act gives you the right to access information held about you. Your right of access can be exercised in accordance with the Act.</p><p>You have the right to receive information about the source, recipients and purposes of your archived personal data at any time without having to pay a fee for such disclosures. You also have the right to demand that your data are rectified, blocked or eradicated. Please do not hesitate to contact us at any time under the address disclosed in section “Economists for Future campaign and website“ on this website if you have questions about this or any other data protection related issues. You also have the right to log a complaint with the competent supervising agency.</p><p>Moreover, under certain circumstances, you have the right to demand the restriction of the processing of your personal data.</p><p>Right to object to the collection of data in special cases; right to object to direct advertising (Art. 21 GDPR)</p><p>In the event that data are processed on the basis of Art. 6 Sect. 1 lit. e or f GDPR, you have the right to at any time object to the processing of your personal data based on grounds arising from your unique situation. This also applies to any profiling based on these provisions. To determine the legal basis, on which any processing of data is based, please consult this Data Protection Declaration. If you log an objection, we will no longer process your affected personal data, unless we are in a position to present compelling protection worthy grounds for the processing of your data, that outweigh your interests, rights and freedoms or if the purpose of the processing is the claiming, exercising or defence of legal entitlements (objection pursuant to Art. 21 Sect. 1 GDPR).</p><p>If your personal data is being processed in order to engage in direct advertising, you have the right to at any time object to the processing of your affected personal data for the purposes of such advertising. This also applies to profiling to the extent that it is affiliated with such direct advertising. If you object, your personal data will subsequently no longer be used for direct advertising purposes (objection pursuant to Art. 21 Sect. 2 GDPR).</p><p>&nbsp;</p><h3>Right to demand processing restrictions</h3><p>You have the right to demand the imposition of restrictions as far as the processing of your personal data is concerned. To do so, you may contact us at any time at the address provided in section „Information Required by Law.“ The right to demand restriction of processing applies in the following cases:</p><ul><li>In the event that you should dispute the correctness of your data archived by us, we will usually need some time to verify this claim. During the time that this investigation is ongoing, you have the right to demand that we restrict the processing of your personal data.</li><li>If the processing of your personal data was/is conducted in an unlawful manner, you have the option to demand the restriction of the processing of your data in lieu of demanding the eradication of this data.</li><li>If we do not need your personal data any longer and you need it to exercise, defend or claim legal entitlements, you have the right to demand the restriction of the processing of your personal data instead of its eradication.</li><li>If you have raised an objection pursuant to Art. 21 Sect. 1 GDPR, your rights and our rights will have to be weighed against each other. As long as it has not been determined whose interests prevail, you have the right to demand a restriction of the processing of your personal data.</li></ul><p>If you have restricted the processing of your personal data, these data – with the exception of their archiving – may be processed only subject to your consent or to claim, exercise or defend legal entitlements or to protect the rights of other natural persons or legal entities or for important public interest reasons cited by the European Union or a member state of the EU.</p><h3>Changes to our privacy policy</h3><p>Any changes we make to our privacy policy in the future will be posted on this page and, where appropriate, notified to you by e-mail. Please check back frequently to see any updates or changes to our privacy policy.</p><h3>Contact</h3><p>Questions, comments and requests regarding this privacy policy are welcomed and should be addressed to info@econ4future.org</p>',
                'structureType' => 'default',
                'navigationContexts' => ['footer'],
            ],
        ];

        return $pages;
    }

    /**
     * @throws DocumentManagerException
     */
    private function loadHomepage(DocumentManager $documentManager): void
    {
        /** @var HomeDocument $homeDocument */
        $homeDocument = $documentManager->find('/cmf/econ4future/contents', AppFixtures::LOCALE_EN);

        $homeDocument->getStructure()->bind(
            [
                'locale' => AppFixtures::LOCALE_EN,
                'title' => $homeDocument->getTitle(),
                'seo' => [
                    'title' => 'Economists for Future - Sign the Open Letter',
                    'description' => 'We are living through a climate emergency and the economics community must urgently step up and help arrest this crisis. Despite some exceptions, the overall contribution from the community has been nowhere near commensurate with the magnitude of the problem. We still have great unmet potential to help tackle this crisis with the whole discipline being involved and taking action NOW. This is an opportunity to address the climate challenge while transforming our world into a more prosperous and equitable place.',
                ],
                'url' => '/',
                'article' => '',
                'main_title' => 'For an economics that takes the climate science seriously.',
                'sub_title' => '#Economists4Future',
                'open_letter_intro' => '<p>We are living through a <em>climate emergency</em> and, among the many profound challenges that this presents, the situation demands that the discipline of economics takes a hard look at itself.</p>',
                'open_letter_block_a' => '<p>Across the globe, young people are marching in the <em>millions</em> to demand that necessary action is taken to avoid catastrophic climate disaster. These climate strikers are calling on everyone to play their part in addressing this crisis.</p>',
                'open_letter_block_b' => '<p>Economists for Future is extending this demand to the economics community. Despite some exceptions, the overall contribution from economists has been nowhere near commensurate with the magnitude of the problem. </p> <p>Where we have contributed, we have been largely unsuccessful: the market-based solutions have yet to deliver the emission cuts at the required speed.</p> <p>In the context of the climate emergency, <em>winning slowly is losing</em>.</p>',
            ]
        );

        $documentManager->persist($homeDocument, AppFixtures::LOCALE_EN);
        $documentManager->publish($homeDocument, AppFixtures::LOCALE_EN);
    }

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager|DocumentManager $manager
     *
     * @throws \Exception
     */
    public function load(ObjectManager $manager)
    {
     
        $parent = $this->manager->find(null, $basepath);
        foreach ($data['static'] as $overview) {
            $page = $this->loadStaticPageData($overview, $basepath, $parent);

            if (isset($overview['blocks']) && is_array($overview['blocks'])) {
                foreach ($overview['blocks'] as $name => $block) {
                    $this->loadBlock($page, $name, $block);
                }
            }
        }

        $this->manager->flush();
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 20;
    }

    /**
     * Sets the container.
     *
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}
