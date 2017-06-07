<?php
/*-----8<--------------------------------------------------------------------
 *
* BEdita - a semantic content management framework
*
* Copyright 2017 ChannelWeb Srl, Chialab Srl
*
*------------------------------------------------------------------->8-----
*/

require_once APP . DS . 'vendors' . DS . 'shells'. DS . 'bedita_base.php';
require_once 'vendors' . DS . 'yaml'. DS . 'autoload.php';

App::import ('Vendor', 'yaml', array ('file' => 'yaml' . DS . 'Yaml.php') );
use Symfony\Component\Yaml\Yaml;

class SpecShell extends BeditaBaseShell {

    /**
     * Merge YAML files and save YAML OpenAPI 2 to spec file $yamlFile
     *
     * @return void
     */
    public function generate() {
        if (empty($this->params['p'])) {
            $this->out('Missing -p <path> parameter');
            $this->help();
            return;
        }
        if (empty($this->params['f'])) {
            $this->out('Missing -f <filename> parameter');
            $this->help();
            return;
        }
        $source = $this->params['p'];
        $yamlFile = $this->params['f'];
        if (file_exists($yamlFile)) {
            $res = $this->in('Overwrite yaml file "' . $yamlFile . '"?', ['y', 'n'], 'n');
            if ($res != 'y') {
                $this->info('Yaml file not updated');
                return;
            }
        }
        $dir = new Folder($source);
        $files = $dir->find('.*\.yaml', true);
        $spec = ['paths' => [], 'definitions' => []];
        foreach ($files as $file) {
            $yamlData = Yaml::parse(file_get_contents($dir->pwd() . DS . $file));
            $spec = array_merge($yamlData, $spec);
            if (!empty($yamlData['paths'])) {
                $spec['paths'] += $yamlData['paths'];
            }
            $spec['definitions'] = array_merge(
                empty($yamlData['definitions']) ? [] : $yamlData['definitions'],
                $spec['definitions']
            );
        }
        $yaml = Yaml::dump($spec, 2, 4, Yaml::DUMP_OBJECT_AS_MAP); // with Symfony 3.3: Yaml::DUMP_EMPTY_ARRAY_AS_SEQUENCE (https://github.com/symfony/symfony/issues/15781#issuecomment-300200486)
        // future patch: use Yaml::DUMP_EMPTY_ARRAY_AS_SEQUENCE (as in following line) and remove str_replace of Bearer {}
        // $yaml = Yaml::dump($yaml, 2, 4, Yaml::DUMP_EMPTY_ARRAY_AS_SEQUENCE);
        $yaml = str_replace("Bearer: {  }", "Bearer: [ ]", $yaml);
        file_put_contents($yamlFile, $yaml);
        $this->out('Yaml file updated ' . $yamlFile);
    }

    public function help() {
        $this->hr();
        $this->out('spec script shell usage:');
        $this->out('');
        $this->out('./cake.sh spec generate -p <path to yaml files to merge> -f <file to generate>');
        $this->out('');
    }
}
?>