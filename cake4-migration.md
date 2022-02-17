# Migration to Cake 4

Follow <https://book.cakephp.org/4/en/appendices/4-0-upgrade-guide.html> with these caveats, also for subsequent releases

* use <https://github.com/bedita/app/blob/master/src/Application.php> as reference for `Application.php` - a complete class review is probably needed
* replace `src/Shell/ConsoleShell.php` with `src/Command/ConsoleCommand.php` from <https://github.com/bedita/app/blob/master/src/Command/ConsoleCommand.php>

Legend

* (**N**) change not generated automatically by migration tool
* (**B**) breaking change between Cake3 and Cake4
* (**B?**) possibile breaking change between Cake3 and Cake4 (to investigate)
* (**D**) deprecations

## Test cases

* public function setUp() ==> public function setUp(): void

* public function tearDown() ==> public function tearDown(): void

* [improvement, not necessary] (**N**) extends ConsoleIntegrationTestCase  ==>  use ConsoleIntegrationTestTrait

* (**N**) $this->fixtureManager ==>  static::$fixtureManager

* remove @expectedException & @expectedExceptionMessage annotations ==> $this->expectException() - $this->expectExceptionMessage()

* (**B?**) static::assertEquals($expected, $relations, '', 0, 10, true) ==>
    static::assertEquals($expected, $relations, '');
    static::assertEqualsCanonicalizing($expected, $relations, '');
    static::assertEqualsWithDelta($expected, $relations, 0, '');

* (**B**) (see below on `Cache::read()`) from `static::assertFalse(Cache::read(...))` => `static::assertNull(Cache::read(...))`

### Fixtures

* (**N**) public function init() ==> public function init(): void

## Src

* (**N**)(**B**) changed signature. `Cache::clear(false, '#####');` ==>  `Cache::clear('#####'');`

* (**N**)(**B**) `App::className()` now returns `null` and not `false` on failure

* (**N**)(**B**) `Cache::read()` now returns `null` and not `false` on data missing/expired or failure

* (**B?**) public function initialize() ==> public function initialize(): void
    controllers, commands,...

* (**N**) public function implementedEvents() ==> public function implementedEvents(): array

* (**N**)(**B?**) public function startup() ==> public function startup(): void

* (**N**) `$this->log($e, 'warning');` where `$e` is an Exception replaced with `$this->log($e->getMessage(), 'warning');`

* (**N**) use `{foo}` instead of `:foo` in routing rules (deprecation)

* (**N**) don't use special chars like `*` in cache key strings

* (**N**) in `bootstrap.php` `I18n::translators()->registerLoader()` must return a `Package` object, not a callable

### Shells & Commands

* (**N**) public function getOptionParser() ==> public function getOptionParser(): ConsoleOptionParser

### Authorize classes

* (**N**) public function authorize($user, ServerRequest $request) ==> public function authorize($user, ServerRequest $request): bool

### Controllers

* (**B**) public function beforeFilter(Event $event) ==> public function beforeFilter(EventInterface $event)
* (**N**)(**B**) from `$this->set('_serialize', ['result'])` to `$this->viewBuilder()->setOption('serialize', ['result'])`
* (**N**)(**B**) `viewVars` are now accessible only via `viewBuilder()`- use `$this->viewBuilder()->getVar()` or `$this->viewBuilder()->hasVar()`

### Components

* (**N**) use `getController()->getRequest()` since `$request` controller attrib is now protected
* (**N**) use `getController()->getResponse()` since `$response` controller attrib is now protected
* (**B?**) `public function initialize(array $config)` ==> `public function initialize(array $config): void`

### Models - Behaviors, Entities, Tables

* (**B?**) `public function initialize(array $config)` ==> `public function initialize(array $config): void`

* (**B**) beforeRules, beforeSave, beforeDelete, beforeFind, afterSave, afterDelete, afterFind methods `Event $event` argument => `EventInterface $event`

#### Tables

* (**N**) `newEntity()` method has changed signature, at least one argument is needed from no args `newEntity()` => `newEntity([])`

* validationDefault(Validator $validator) => validationDefault(Validator $validator): Validator

* buildRules(RulesChecker $rules) => buildRules(RulesChecker $rules): \Cake\ORM\RulesChecker

* (**N**) (**B**) `protected function _initializeSchema(TableSchema $schema)` ==> `protected function _initializeSchema(TableSchemaInterface $schema): TableSchemaInterface`

#### Entities

* (**N**)(**B**) `unsetProperty()` => `unset()`
* (**D**) `isNew($value)` => `setNew($value)`

### Middleware

* (**D**) should implement `MiddlewareInterface` with `process()` method instead of `__invoke()`

### View

* (**N**) in `JsonApiView:72` use ```parent::_dataToSerialize($serialize)``` - `JsonView::_dataToSerialize()` changed signature
