export type SelectorType = 'css' | 'text' | 'role' | 'testId' | 'xpath';
export type AssertionType = 'text' | 'visible' | 'hidden' | 'count' | 'url' | 'attribute';
export type WaitType = 'visible' | 'hidden' | 'attached' | 'detached' | 'url' | 'loadstate';
export type OnFail = 'stop' | 'continue' | 'skip_dependents';
export type ScenarioMode = 'pr' | 'nightly' | 'release' | 'ad-hoc';
export type SaveSource = 'text' | 'value' | 'attribute' | 'url';
export type StepStatus = 'passed' | 'failed' | 'skipped';

export interface Selector {
  type: SelectorType;
  value: string;
  name?: string;
}

export interface AssertionText {
  type: 'text';
  selector: Selector;
  value: string;
}

export interface AssertionVisible {
  type: 'visible';
  selector: Selector;
}

export interface AssertionHidden {
  type: 'hidden';
  selector: Selector;
}

export interface AssertionCount {
  type: 'count';
  selector: Selector;
  value: number;
}

export interface AssertionUrl {
  type: 'url';
  value: string;
}

export interface AssertionAttribute {
  type: 'attribute';
  selector: Selector;
  name: string;
  value: string;
}

export type Assertion =
  | AssertionText
  | AssertionVisible
  | AssertionHidden
  | AssertionCount
  | AssertionUrl
  | AssertionAttribute;

export interface WaitSelector {
  type: 'visible' | 'hidden' | 'attached' | 'detached';
  selector: Selector;
  timeout?: number;
}

export interface WaitUrl {
  type: 'url';
  value: string;
}

export interface WaitLoadstate {
  type: 'loadstate';
  value: 'load' | 'domcontentloaded' | 'networkidle';
}

export type Wait = WaitSelector | WaitUrl | WaitLoadstate;

export interface Save {
  source: SaveSource;
  selector?: Selector;
  attribute?: string;
  targetVar: string;
}

export interface StepCommon {
  id: string;
  description?: string;
  dependsOn?: string[];
  timeout?: number;
  retry?: number;
  onFail?: OnFail;
  wait?: Wait;
  assert?: Assertion[];
  save?: Save;
}

export interface ActionGoto extends StepCommon {
  action: 'goto';
  url: string;
}

export interface ActionFill extends StepCommon {
  action: 'fill';
  selector: Selector;
  value: string;
}

export interface ActionClick extends StepCommon {
  action: 'click';
  selector: Selector;
}

export interface ActionSelect extends StepCommon {
  action: 'select';
  selector: Selector;
  value: string;
}

export interface ActionWaitFor extends StepCommon {
  action: 'waitFor';
  wait: Wait;
}

export interface ActionAssert extends StepCommon {
  action: 'assert';
  assertions: Assertion[];
}

export interface ActionSave extends StepCommon {
  action: 'save';
  save: Save;
}

export interface ActionLoginAdmin extends StepCommon {
  action: 'loginAdmin';
  email?: string;
  password?: string;
}

export interface ActionOpenResource extends StepCommon {
  action: 'openResource';
  resource: string;
  id?: string | number;
}

export interface ActionDeleteRecordByTitle extends StepCommon {
  action: 'deleteRecordByTitle';
  resource: string;
  title: string;
}

export type Step =
  | ActionGoto
  | ActionFill
  | ActionClick
  | ActionSelect
  | ActionWaitFor
  | ActionAssert
  | ActionSave
  | ActionLoginAdmin
  | ActionOpenResource
  | ActionDeleteRecordByTitle;

export interface VariableEnv {
  source: 'env';
  key: string;
  default?: string;
}

export interface VariableLiteral {
  source: 'literal';
  value: string;
}

export interface VariableGenerated {
  source: 'generated';
  type: 'uuid' | 'timestamp' | 'random_string';
  length?: number;
}

export type Variable = VariableEnv | VariableLiteral | VariableGenerated;

export interface Runtime {
  viewport?: { width: number; height: number };
  device?: string;
  storageState?: string;
  locale?: string;
  timezone?: string;
  cookies?: Array<{ name: string; value: string; domain: string }>;
}

export interface StepDefaults {
  timeout?: number;
  retry?: number;
  onFail?: OnFail;
}

export interface Scenario {
  scenarioId: string;
  version: 2;
  description?: string;
  mode: ScenarioMode[];
  tags?: string[];
  defaults?: StepDefaults;
  runtime?: Runtime;
  variables?: Record<string, Variable>;
  beforeAll?: Step[];
  afterAll?: Step[];
  steps: Step[];
}

export interface RunContext {
  vars: Record<string, string>;
  stepResults: Record<string, StepStatus>;
}

export interface StepReport {
  stepId: string;
  description?: string;
  status: StepStatus;
  duration: number;
  error?: string;
  retries: number;
}

export interface ScenarioReport {
  scenarioId: string;
  status: StepStatus;
  duration: number;
  mode: ScenarioMode[];
  tags: string[];
  runAt: string;
  steps: StepReport[];
  artifacts?: {
    trace?: string;
    screenshot?: string;
  };
}
