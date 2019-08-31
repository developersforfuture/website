<?php

namespace App\Document;


use Doctrine\ODM\PHPCR\Mapping\Annotations as PHPCRODM;
use App\Services\Utils;
use Symfony\Cmf\Bundle\ContentBundle\Doctrine\Phpcr\StaticContent;
use Symfony\Cmf\Bundle\SeoBundle\Doctrine\Phpcr\SeoMetadata;
use Symfony\Cmf\Bundle\SeoBundle\Extractor\DescriptionReadInterface;
use Symfony\Cmf\Bundle\SeoBundle\Extractor\TitleReadInterface;
use Symfony\Cmf\Bundle\SeoBundle\Model\SeoMetadataInterface;
use Symfony\Cmf\Bundle\SeoBundle\SeoAwareInterface;
use Symfony\Cmf\Bundle\SeoBundle\SitemapAwareInterface;

/**
 * @PHPCRODM\Document(referenceable=true)
 *
 * @author Maximilian Berghoff <Maximilian.Berghoff@mayflower.de>
 */
class StaticPage extends StaticContent implements SitemapAwareInterface, SeoAwareInterface, TitleReadInterface, DescriptionReadInterface
{
    /**
     * @var bool
     * @PHPCRODM\Boolean()
     */
    private $isVisibleForSitemap = true;
    /**
     * @var SeoMetadataInterface
     *
     * @PHPCRODM\Child
     */
    private $seoMetadata;

    /**
     * @var string
     * @PHPCRODM\Field(type="string")
     */
    private $description;

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * Decision whether a document should be visible
     * in sitemap or not.
     *
     * @param string $sitemap
     *
     * @return bool
     */
    public function isVisibleInSitemap($sitemap): bool
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isVisibleForSitemap(): bool
    {
        return $this->isVisibleForSitemap;
    }

    /**
     * @param bool $isVisibleForSitemap
     */
    public function setIsVisibleForSitemap(bool $isVisibleForSitemap): void
    {
        $this->isVisibleForSitemap = $isVisibleForSitemap;
    }

    /**
     *
     */
    public function prePersist(): void
    {
        if (null !== $this->getTitle() && !empty($this->title)) {
            $this->setName(Utils::createSeoUrl($this->getTitle()));
        }
    }

    /**
     * Gets the SEO metadata for this content.
     *
     * @return SeoMetadataInterface
     */
    public function getSeoMetadata(): SeoMetadataInterface
    {
        return $this->seoMetadata;
    }

    /**
     * Sets the SEO metadata for this content.
     *
     * This method is used by a listener, which converts the metadata to a
     * plain array in order to persist it and converts it back when the content
     * is fetched.
     *
     * @param SeoMetadataInterface $metadata
     */
    public function setSeoMetadata($metadata): void
    {
        $this->seoMetadata = $metadata;
    }

    /**
     * Provides a title of this page to be used in SEO context.
     *
     * @return string
     */
    public function getSeoTitle(): string
    {
        return $this->getTitle();
    }

    /**
     * Provide a description of this page to be used in SEO context.
     *
     * @return string
     */
    public function getSeoDescription(): ?string
    {
        return $this->description;
    }
}
