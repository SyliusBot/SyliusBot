<?php

namespace SyliusBot\Crowdin;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class TranslationPathTransformer implements TranslationPathTransformerInterface
{
    /**
     * @var string
     */
    private $defaultLocale;

    /**
     * @param string $defaultLocale
     */
    public function __construct($defaultLocale)
    {
        $this->defaultLocale = $defaultLocale;
    }

    /**
     * {@inheritdoc}
     */
    public function transformLocalPathToCrowdinPath($localPath)
    {
        $matched = preg_match('/^(?:.*\/)?(.+Bundle)\/Resources\/translations\/(.+\.[a-zA-Z_]+\..+)$/', $localPath, $matches);

        if (!$matched) {
            throw new \InvalidArgumentException(sprintf(
                'Given local path "%s" could not be transformed to Crowdin path',
                $localPath
            ));
        }

        return sprintf("/%s/%s", $matches[1], $matches[2]);
    }

    /**
     * {@inheritdoc}
     */
    public function transformCrowdinPathToLocalPath($crowdinPath)
    {
        // Write-only regexp, don't touch until it crashes :)
        $matched = preg_match('/^\/?(?:(?P<language>[a-zA-z\-_]{2,5})\/)?(?P<bundle>.+Bundle)\/(?P<path>.+\.[a-zA-Z_]+\..+)$/', $crowdinPath, $matches);

        if (!$matched) {
            throw new \InvalidArgumentException(sprintf(
                'Given Crowdin path "%s" could not be transformed to local path',
                $crowdinPath
            ));
        }

        $language = !empty($matches['language']) ? str_replace('-', '_', $matches['language']) : $this->defaultLocale;
        $path = str_replace('.' . $this->defaultLocale . '.',  '.' . $language . '.', $matches['path']);

        return sprintf('src/Sylius/Bundle/%s/Resources/translations/%s', $matches['bundle'], $path);
    }
}
