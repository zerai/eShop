<?php declare(strict_types=1);

use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use PhpCsFixer\Fixer\FunctionNotation\NativeFunctionInvocationFixer;
use PhpCsFixer\Fixer\Import\FullyQualifiedStrictTypesFixer;
use PhpCsFixer\Fixer\Import\NoUnusedImportsFixer;
use PhpCsFixer\Fixer\Import\OrderedImportsFixer;
use PhpCsFixer\Fixer\NamespaceNotation\BlankLineAfterNamespaceFixer;
use PhpCsFixer\Fixer\PhpTag\BlankLineAfterOpeningTagFixer;
use PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer;
use PhpCsFixer\Fixer\Strict\StrictComparisonFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->paths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
        __DIR__ . '/_basket/src',
        __DIR__ . '/_basket/tests',
        __DIR__ . '/_catalog/src',
        __DIR__ . '/_catalog/tests',
        __DIR__ . '/_iam/src',
        __DIR__ . '/_iam/tests',
        __DIR__ . '/ecs.php',
        __DIR__ . '/rector.php',
    ]);

    $ecsConfig->skip([
        __DIR__ . '/src/Kernel.php',
        __DIR__ . '/tests/bootstrap.php',
        BlankLineAfterOpeningTagFixer::class,
    ]);

    $ecsConfig->rules([
        BlankLineAfterNamespaceFixer::class,
        DeclareStrictTypesFixer::class,
        FullyQualifiedStrictTypesFixer::class,
        NativeFunctionInvocationFixer::class,
        NoUnusedImportsFixer::class,
        OrderedImportsFixer::class,
        StrictComparisonFixer::class,
    ]);

    $ecsConfig->ruleWithConfiguration(ArraySyntaxFixer::class, [
        'syntax' => 'short',
    ]);

    $ecsConfig->sets([
        SetList::ARRAY,
        SetList::DOCBLOCK,
        SetList::NAMESPACES,
        SetList::SPACES,
        SetList::PHPUNIT,
        SetList::PSR_12,
    ]);

    //    $ecsConfig->ruleWithConfiguration(PhpUnitTestAnnotationFixer::class, [
    //        'style' => 'annotation',
    //    ]);
};
