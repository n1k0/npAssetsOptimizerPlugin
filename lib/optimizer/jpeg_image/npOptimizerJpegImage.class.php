<?php
/**
 * npAssetsOptimizerPlugin JPEG images optimizer
 *
 * @package     npAssetsOptimizerPlugin
 * @subpackage  optimizer
 * @author      Clément Herreman <clement.herreman@gmail.com>
 */
class npOptimizerJpegImage extends npOptimizerBase
{
  /**
   * @see npOptimizerBase
   */
  public function configure(array $configuration = array())
  {
    parent::configure($configuration);

    if (isset($configuration['files']))
    {
      parent::setFiles($configuration['files']);
    }
    else if (isset($configuration['folders']))
    {
      parent::setFiles($this->findJpegImages($configuration['folders']));
    }
    else
    {
      throw new sfConfigurationException('You must define either a "files" or a "folders" option to use this optimizer');
    }

    // JPEG images will be replaced by their optimized versions
    $this->replaceFiles = true;
  }

  /**
   * Finds JPEG images within provided absolute paths
   *
   * @param  array  $folders  An array of absoulte folder paths
   *
   * @return array of files absolutes paths to JPEG images
   */
  public function findJpegImages(array $folders = array())
  {
    $files = array();

    foreach ($folders as $folder)
    {
      if (!is_dir($folder))
      {
        throw new InvalidArgumentException(sprintf('"%s" is not a valid readable directory'));
      }

      foreach (sfFinder::type('file')->name('/\.jpe?g$/i')->in($folder) as $file)
      {
        $files[] = $file;
      }
    }

    return $files;
  }

  /**
   * @see npOptimizerBase
   */
  public function getAssetFilepath($file)
  {
    return $file;
  }
}
