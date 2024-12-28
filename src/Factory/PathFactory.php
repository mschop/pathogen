<?php

namespace Pathogen\Factory;

use Pathogen\AbsoluteDriveAnchoredPath;
use Pathogen\AbsolutePath;
use Pathogen\DriveAnchoredInterface;
use Pathogen\Exception\MissingDriveException;
use Pathogen\Exception\PathTypeMismatchException;
use Pathogen\Parsing\ParseOptions;
use Pathogen\Parsing\Parser;
use Pathogen\Parsing\ParserInterface;
use Pathogen\Path;
use Pathogen\PathType;
use Pathogen\RelativeDriveAnchoredPath;
use Pathogen\RelativePath;


class PathFactory implements PathFactoryInterface
{
    protected static ?self $defaultInstance = null;

    public function __construct(
        protected ParserInterface $parser,
    )
    {
    }

    public static function getDefaultInstance(): self
    {
        if (self::$defaultInstance === null) {
            self::$defaultInstance = new self(new Parser);
        }
        return self::$defaultInstance;
    }

    public static function setDefaultInstance(self $pathFactory): void
    {
        self::$defaultInstance = $pathFactory;
    }

    /**
     * @inheritdoc
     */
    public function fromString(string $path, string $type): Path
    {
        $expectsDrive = is_a($type, DriveAnchoredInterface::class, true);
        $options = new ParseOptions(parseWindowsDrive: $expectsDrive);
        $parsingResult = $this->parser->parse($path, $options);

        if ($parsingResult->drive === null && $expectsDrive) {
            throw new MissingDriveException("Path '$path' is expected to have a drive, but has none");
        }

        if (is_a($type, AbsolutePath::class, true) && $parsingResult->pathType !== PathType::ABSOLUTE) {
            throw new PathTypeMismatchException("Expected absolute path but relative path was provided");
        }

        if (is_a($type, RelativePath::class, true) && $parsingResult->pathType !== PathType::RELATIVE) {
            throw new PathTypeMismatchException("Expected relative path but absolute path was provided");
        }

        if ($type === Path::class) {
            $type = match($parsingResult->pathType) {
                PathType::ABSOLUTE => AbsolutePath::class,
                PathType::RELATIVE => RelativePath::class,
            };
        }

        return $this->fromAtoms($parsingResult->atoms, $type, $parsingResult->hasTrailingSeparator, $parsingResult->drive);
    }

    /**
     * @inheritdoc
     */
    public function fromAtoms(array $atoms, string $type, bool $hasTrailingSeparator, ?string $drive = null): Path
    {
        $expectsDrive = is_a($type, DriveAnchoredInterface::class, true);

        if ($expectsDrive && $drive === null) {
            throw new MissingDriveException("You cannot instantiate drive anchored path without giving a drive");
        }

        return $expectsDrive
            ? new $type($atoms, $hasTrailingSeparator, $drive)
            : new $type($atoms, $hasTrailingSeparator);
    }
}
