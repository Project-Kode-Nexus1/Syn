<?php

declare(strict_types=1);

// PVCpu Opcodes
final class PVCpuOpcodes {
	public const int OP_NOP = 0x0;

	// ALU
	public const int OP_ADD = 0x1;
	public const int OP_SUB = 0x2;
	public const int OP_MUL = 0x3;
	public const int OP_DIV = 0x4;
	public const int OP_CMP = 0x5;
	public const int OP_UCMP = 0x6;
	public const int OP_AND = 0x7;
	public const int OP_OR = 0x8;
	public const int OP_NOT = 0x9;
	public const int OP_NAND = 0xA;
	public const int OP_NOR = 0xB;
	public const int OP_XOR = 0xC;
	public const int OP_SHL = 0xE;
	public const int OP_SHR = 0xF;
	public const int OP_ROTL = 0x10;
	public const int OP_ROTR = 0x11;
	public const int OP_AROTL = 0x12;
	public const int OP_AROTR = 0x13;
	public const int OP_INC = 0x14;
	public const int OP_DEC = 0x15;
	public const int OP_TEST = 0x16;

    // Memory
    public const int OP_LOAD = 0x100;
    public const int OP_STORE = 0x101;
	public const int OP_PUSH = 0x102;
	public const int OP_POP = 0x103;
	public const int OP_PUSH16 = 0x104;
	public const int OP_POP16 = 0x105;
	public const int OP_PUSH32 = 0x106;
	public const int OP_POP32 = 0x107;
	public const int OP_PUSH64 = 0x108;
	public const int OP_POP64 = 0x109;
	public const int OP_MSET = 0x10A;
	public const int OP_MCPY = 0x10B;
	public const int OP_MCMP = 0x10C;	

    // Movement
	public const int OP_MOV = 0x115;
	public const int OP_MOVB = 0x116;
	public const int OP_MOVW = 0x117;
	public const int OP_MOVD = 0x118;
	public const int OP_MOVQ = 0x119;
	public const int OP_XCHG = 0x11A;
	public const int OP_RREG = 0x11B;

	// Jumping
	public const int OP_JMP = 0x12C;
	public const int OP_CALL = 0x12D;
	public const int OP_RET = 0x12E;
	public const int OP_EXCEPTION = 0x12F;
	public const int OP_JZ = 0x130;
	public const int OP_JNZ = 0x131;
	public const int OP_JL = 0x132;
	public const int OP_JLE = 0x133;
	public const int OP_JG = 0x134;
	public const int OP_JGE = 0x135;
}

// PVCpu Modes
final class PVCpuModes {
    public const int NULL_MODE = 0x0; // No mode
    public const int REG_REG = 0x1; // dest = src
    public const int REG_IMM = 0x2; // src is actually a imm! dest is a reg (dest = src (as imm))
    public const int REG_EXTIMM = 0x3; // Allows use of Bit 1 of Flags (dest = imm)
    public const int REG_DISP = 0x4; // Allows use of Bit 2 of Flags (dest = mem[disp + PC])
    public const int LOAD_REGADDR = 0x5; // dest = mem[src]
    public const int LOAD_IMMADDR = 0x6; // dest = mem[imm]
    public const int LOAD_PC_REL = 0x7; // dest = mem[src (as offset) + PC]
    public const int STORE_REGADDR = 0x8; // mem[dest] = src
    public const int STORE_IMMADDR = 0x9; // mem[imm] = src
    public const int STORE_PC_REL = 0xA; // mem[dest (as offset) + PC] = src
    // Special
    public const int SRC_REG = 0xB; // opcode (src)
    public const int SRC_REG_IMM = 0xC; // opcode (src (as imm))
    public const int SRC_IMM = 0xD; // opcode (imm)
}

// PVCpu Privilage Levels
final class PVCpuPrivilageLevels {
	public const int PL_FULL = 0;
	public const int PL_HYPERVISOR = 1;
	public const int PL_FIRMWARE = 2;
	public const int PL_KERNEL = 3;
	public const int PL_DRIVERS = 4;
	public const int PL_TRUSTED = 5;
	public const int PL_USER = 6;
	public const int PL_SANDBOX = 7;
}

class PVCpuRegfile {
	// Reg file <array[str, int]>
	private array $registers = [];
	private const array REGFILE_MAP = [
		1 => "g0",
		2 => "g1",
		3 => "g2",
		4 => "g3",
		5 => "g4",
		6 => "g5",
		7 => "g6",
		8 => "g7",
		9 => "g8",
		10 => "g9",
		11 => "g10",
		12 => "g11",
		13 => "g12",
		14 => "g13",
		15 => "g14",
		16 => "g15",
		17 => "g16",
		18 => "g17",
		19 => "g18",
		20 => "g19",
		21 => "g20",
		22 => "g21",
		23 => "g22",
		24 => "g23",
		25 => "g24",
		26 => "g25",
		27 => "g26",
		28 => "g27",
		29 => "g28",
		30 => "g29",
		31 => "g30",
		32 => "lr",
		33 => "sf",
		34 => "sp"
	];

	// Returns if register exists
	public function does_register_exist(int $addr) : bool {
		if (isset(self::REGFILE_MAP[$addr])) {
			$regname = self::REGFILE_MAP[$addr];
			if (isset($this->registers[$regname])) {
				return true;
			}
		}
		return false;
	}

	// Retrieves the Register value
	public function get_register_value(int $addr) : int {
		if ($this->does_register_exist($addr)) {
			$regname = self::REGFILE_MAP[$addr];
			return $this->registers[$regname];
		}
		return -1;
	}

	// Sets/Updates the value of a register
	public function set_register_value(int $addr, int $value) : bool {
		if ($this->does_register_exist($addr)) {
			$regname = self::REGFILE_MAP[$addr];
			$this->registers[$regname] = $value;
			return true;
		}
		return false;
	}

	// Resets all registers
	public function reset() : void {
		for ($i = 0; $i < 31; $i++) {
			$this->registers["g{$i}"] = 0;
		}
		$this->registers['lr'] = 0;
		$this->registers['sf'] = 0;
		$this->registers['sp'] = 0xFFFF;
	}

	public function __construct() {
		$this->reset();
	}
}

class PVCpuIState {
	// IState Reg file <array[str, int]>
	private array $registers = [];
	private const array REGFILE_MAP = [
		// 35 is PC, aka not allowed for access
		36 => "i0",
		37 => "i1",
		38 => "i2",
		39 => "tr",
	];

	// Returns if register exists
	public function does_register_exist(int $addr) : bool {
		if (isset(self::REGFILE_MAP[$addr])) {
			$regname = self::REGFILE_MAP[$addr];
			if (isset($this->registers[$regname])) {
				return true;
			}
		}
		return false;
	}

	// Retrieves the Register value
	public function get_register_value(int $addr) : int {
		if ($this->does_register_exist($addr)) {
			$regname = self::REGFILE_MAP[$addr];
			return $this->registers[$regname];
		}
		return -1;
	}

	// Sets/Updates the value of a register
	public function set_register_value(int $addr, int $value) : bool {
		if ($this->does_register_exist($addr)) {
			$regname = self::REGFILE_MAP[$addr];
			$this->registers[$regname] = $value;
			return true;
		}
		return false;
	}

	// Resets all registers
	public function reset() : void {
		for ($i = 0; $i < 3; $i++) {
			$this->registers["i{$i}"] = 0;
		}
		$this->registers['tr'] = 0;
	}

	public function __construct() {
		$this->reset();
	}
}

// NOTE: Uses PVCpu Architecture
class SynCpu {
	private const VERSION = "1.0.0";

	private int $privilage_level;

	private PVCpuRegfile $regfile;
	private PVCpuIState $istate;

	private bool $halted = false;

	public function __construct() {
		$this->regfile = new PVCpuRegfile();
		$this->istate = new PVCpuIState();
		$this->reset();
	}

	// Resets the CPU
	public function reset() : void {
		$this->halted = false;

		// Reset PL
		$this->privilage_level = PVCpuPrivilageLevels::PL_FULL;

		// Reset Registers
		$this->regfile->reset();
		$this->istate->reset();

		echo "SynCPU Reset Complete!\n";
	}

	// Arithmetic and Logic Unit : Returns array<c, is_zero, is_carry, is_equal>
	private function alu(int $a, int $b, int $opcode) : array {
		$c = 0;
		$is_carry = false;
		$is_equal = $a === $b;

		switch ($opcode) {
			case PVCpuOpcodes::OP_ADD: {
				$c = $a + $b;
				if ($a >= 0 && $b >= 0 && $c < 0) {
					$is_carry = true;
				}

				break;
			}
			case PVCpuOpcodes::OP_SUB: {
				$c = $a - $b;
				if ($a >= 0 && $b >= 0 && $a < $b) {
					$is_carry = true;
				}

				break;
			}
			case PVCpuOpcodes::OP_MUL: {
				$c = $a * $b;
				break;
			}
			case PVCpuOpcodes::OP_DIV: {
				if ($b === 0) return [0, true, false];

				$c = intdiv($a, $b);
				break;
			}

			case PVCpuOpcodes::OP_AND: {
				$c = $a & $b;
				break;
			}
			case PVCpuOpcodes::OP_OR: {
				$c = $a | $b;
				break;
			}
			case PVCpuOpcodes::OP_NOR: {
				$c = ~($a | $b);
				break;
			}
			case PVCpuOpcodes::OP_XOR: {
				$c = $a ^ $b;
				break;
			}
			case PVCpuOpcodes::OP_XNOR: {
				$c = ~($a ^ $b);
				break;
			}
			case PVCpuOpcodes::OP_NOT: {
				$c = ~$a;
				break;
			}
			case PVCpuOpcodes::OP_NAND: {
				$c = ~($a & $b);
				break;
			}

			case PVCpuOpcodes::OP_CMP: {
				break;
			}
			case PVCpuOpcodes::OP_UCMP: {
				break;
			}
			case PVCpuOpcodes::OP_TEST: {
				$c = $a & $b;
				break;
			}
			case PVCpuOpcodes::OP_RSHIFT: {
				$shift = $b & 0x3F;

				if ($shift === 0) {
					$c = $a;
				} else {
					if ($a < 0) {
						$c = ($a >> $shift) & ((1 << (63 - $shift)) - 1);
					} else {
						$c = $a >> $shift;
					}
				}

				break;
			}
			case PVCpuOpcodes::OP_LSHIFT: {
				$shift = $b & 0x3F;

				if ($shift === 0) {
					$c = $a;
				} else {
					$c = $a << $shift;
				}

				break;
			}
			case PVCpuOpcodes::OP_ARSHIFT: {
				$shift = $b & 0x3F;
				$c = $a >> $shift;
				break;
			}
			case PVCpuOpcodes::OP_ARLSHIFT: {
				$shift = $b & 0x3F;
				$c = $a << $shift;
				break;
			}
			case PVCpuOpcodes::OP_ROTR: {
				$shift = $b & 0x3F;

				if ($shift === 0) {
					$c = $a;
				} else {
					$left = 64 - $shift;
					$c = ($a >> $shift) | ($a << $left);
				}

				break;
			}
			case PVCpuOpcodes::OP_ROTL: {
				$shift = $b & 0x3F;

				if ($shift === 0) {
					$c = $a;
				} else {
					$right = 64 - $shift;
					$c = ($a << $shift) | ($a >> $right);
				}

				break;
			}

			default: break;
		}

		return [$c, $c == 0, $is_carry, $is_equal];
	}

	// Gets value based on mode
	private function get_value_via_mode(int $mode, int $flags, int $extra, int $rsrc, int $rdest) : int {
		switch ($mode) {
			case PVCpuModes::REG_REG: return $this->regfile->get_register_value($rsrc);
			case PVCpuModes::REG_IMM: return $rsrc;
			
			case PVCpuModes::REG_EXTIMM: {
				if (!($flags & 0b0010)) return -1; // Bit 1 <Extension Present Bit>

				return $flags & 0b0100 ? ($extra & 0xFFFFFFFFFFFFFFFF) : ($extra & 0xFFFFFFFF); // Bit 2 <Extension is 64-bits Bit>
			}

			default: return -1;
		}
	}

	// Saves value based on mode
	private function save_value_via_mode(int $mode, int $flags, int $extra, int $rsrc, int $rdest, int $value) : bool {
		switch ($mode) {
			case PVCpuModes::REG_REG:
			case PVCpuModes::REG_IMM:
			case PVCpuModes::REG_EXTIMM: {
				return $this->regfile->set_register_value($rdest, $value);
			}

			default: return false;
		}
	}

	// Raises an exception
	private function raise_exception(int $type) : void {
		// Since i did not yet implement the CPU Info structure, ill just directly halt
		$this->halted = true;

		echo "SynCPU Halted!\n";
	}

	// Executes machine code
	public function execute(int $inst, int $extra) : void {
		if ($this->halted) return;

		// Break instruction
		$opcode = ($inst >> 20) & 0xFFF;
		$mode = ($inst >> 16) & 0xF;
		$rsrc = ($inst >> 10) & 0x3F;
		$rdst = ($inst >> 4) & 0x3F;
		$flags = $inst & 0xF;

		if (!($flags & 0b0001)) { // Bit 0 <Valid Instruction Bit>
			$this->raise_exception(0); // TODO: Add proper enum
			return;
		}

		$out_v = 0;

		$src_v = $this->get_value_via_mode($mode, $flags, $extra, $rsrc, $rdst);
		if ($src_v < 0) {
			$this->raise_exception(0); // TODO: Add proper enum
			return;
		}

		$dest_v = $this->get_value_via_mode($mode, $flags, $extra, $rsrc, $rdst);
		if ($dest_v < 0) {
			$this->raise_exception(0); // TODO: Add proper enum
			return;
		}

		// ALU Instructions
		if ($opcode >= PVCpuOpcodes::OP_ADD && $opcode <= PVCpuOpcodes::OP_ROTL) {
			$out_v = $this->alu($src_v, $dest_v, $opcode)[0];
			goto label_out;
		}

		// Other instructions
		switch ($opcode) {
			case PVCpuOpcodes::OP_NOP: return; // don't do anything

			case PVCpuOpcodes::OP_MOVQ:
			case PVCpuOpcodes::OP_MOV: $out_v = $src_v; break;
			case PVCpuOpcodes::OP_MOVB: $out_v = $src_v & 0xFF; break;
			case PVCpuOpcodes::OP_MOVW: $out_v = $src_v & 0xFFFF; break;
			case PVCpuOpcodes::OP_MOVD: $out_v = $src_v & 0xFFFFFFFF; break;
			case PVCpuOpcodes::OP_XCHG: {
				$out_v = $dest_v;

				// Exchange
				if (!$this->save_value_via_mode($mode, $flags, $extra, $rdst, $rsrc, $out_v)) {
					$this->raise_exception(0); // TODO: Add proper enum
					return;
				}

				$out_v = $src_v;
				break;
			}
			case PVCpuOpcodes::OP_RREG: $out_v = 0; break;
			
			default: {
				$this->raise_exception(0); // TODO: Add proper enum
				return;
			}
		}

		label_out: {
			if (!$this->save_value_via_mode($mode, $flags, $extra, $rsrc, $rdst, $out_v)) {
				$this->raise_exception(0); // TODO: Add proper enum
				return;
			}
		}
	}
}

$cpu = new SynCpu();

$filePath = __DIR__ . '/tests/bin/t1.bin';
if (is_file($filePath)) {
    $data = file_get_contents($filePath);

    if ($data !== false) {
        $length = strlen($data);

        for ($offset = 0; $offset < $length; $offset += 4) {
            $chunk = unpack('V', substr($data, $offset, 4))[1];
            $cpu->execute($chunk, 0x0);
        }
    }
}

?>