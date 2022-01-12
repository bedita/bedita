# Cake 4 migration notes

(N) non generata da tool di migrazione automatica
(B) breaking change tra Cake3 e Cake4
(B?) possibile breaking change tra Cake3 e Cake4 (da capire)

## Test cases

* // miglioria per deprecation non obbligatoria (N) extends ConsoleIntegrationTestCase  ==>  use ConsoleIntegrationTestTrait;

* (N) $this->fixtureManager ==>  static::$fixtureManager

* public function setUp() ==> public function setUp(): void

* public function tearDown() ==> public function tearDown(): void

* @expectedException &  @expectedExceptionMessage ==> $this->expectException - $this->expectExceptionMessage

* (B?) static::assertEquals($expected, $relations, '', 0, 10, true) ==>
    static::assertEquals($expected, $relations, '');
    static::assertEqualsCanonicalizing($expected, $relations, '');
    static::assertEqualsWithDelta($expected, $relations, 0, '');

### Fixtures

* (N) public function init() ==> public function init(): void

## Src

* (N) Cache::clear(false, '#####'); ==>  Cache::clear('#####'');

* public function initialize() ==> public function initialize(): void
    controllers, commands,...

* (N) public function implementedEvents() ==> public function implementedEvents(): array

* (N) public function startup() ==> public function startup(): void

### Shells & Commands

* (N) public function getOptionParser() ==> public function getOptionParser(): ConsoleOptionParser

### EndpointAuthorize

* (N) public function authorize($user, ServerRequest $request): bool

### Controllers

* (B) public function beforeFilter(Event $event) ==> public function beforeFilter(EventInterface $event)

### Components

* public function initialize(array $config) ==> public function initialize(array $config): void

### Models - Behaviors, Entities, Tables

* public function initialize(array $config) ==> public function initialize(array $config): void

* (B) beforeRules, beforeSave, beforeDelete, beforeFind, afterSave, afterDelete, afterFind methods `Event $event` argument => `EventInterface $event`

#### Tables

* validationDefault(Validator $validator) => validationDefault(Validator $validator): Validator

* buildRules(RulesChecker $rules) => buildRules(RulesChecker $rules): \Cake\ORM\RulesChecker

* (N) (B) protected function _initializeSchema(TableSchema $schema) ==> protected function _initializeSchema(TableSchemaInterface $schema): TableSchemaInterface
