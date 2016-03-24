<?php
/**
 ${BEDITA_LICENSE_HEADER}
 */
namespace BEdita\Core\Shell;

use Cake\Console\Shell;

/**
 * Automatic documentation generation
 */
class DocGenShell extends Shell
{

    /**
     * {@inheritDoc}
     */
    public function getOptionParser()
    {
        $parser = parent::getOptionParser();
        $parser->description('Generate documentation from shell');
        $parser->addSubcommand('doc', [
            'help' => 'Generate documentation for shell command',
            'parser' => [
                'description' => [
                    'Generate documentation in RST format for a BEdita Shell',
                ],
            ],
        ]);
        return $parser;
    }

    /**
     * Doc
     *
     * @return void
     */
    public function doc()
    {
        $shells = ['DbAdminShell', 'DocGenShell'];
        foreach ($shells as $sh) {
            $sh = __NAMESPACE__ . '\\' . $sh;
            $shell = new $sh;
            $parser = $shell->getOptionParser();
            $description = $parser->description();
            $this->info($description);
        }
    }
}
